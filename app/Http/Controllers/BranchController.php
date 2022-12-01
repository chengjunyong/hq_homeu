<?php

namespace App\Http\Controllers;

use App\User;
use App\Branch;
use App\Do_list;
use App\Supplier;
use App\Do_detail;
use App\Transaction;
use App\Do_configure;
use App\Product_list;
use App\Branch_product;
use App\Tmp_order_list;
use App\Warehouse_stock;
use App\Product_configure;
use App\Stock_lost_history;
use App\Transaction_detail;
use Illuminate\Http\Request;
use App\Branch_stock_history;
use App\Damaged_stock_history;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class BranchController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth', 'user_access'], ['except' => ['branchSync', 'branchSyncCompleted', 'createTesting']]);
  }

  public function getBranch()
  {
    $url = route('home')."?p=branch_menu";


    $branch = Branch::paginate(6);

    return view('branch',compact('branch','url'));
  }

  public function createBranch(Request $request)
  {
    $branch_id = Branch::create([
      'branch_name' => $request->branch_name,
      'address' => $request->address,
      'contact' => $request->contact_number,
      'token' => $request->token
    ]);

    $product_list = Product_list::select('department_id','category_id','barcode','product_name','uom','cost','price','reorder_level','recommend_quantity','unit_type')
                                 ->get()
                                 ->toArray();

    foreach($product_list as $key => $result){
      $product_list[$key]['branch_id'] = $branch_id->id;
      $product_list[$key]['created_at'] = date('Y-m-d H:i:s');
      $product_list[$key]['updated_at'] = date('Y-m-d H:i:s');
    }

    $product_list = array_chunk($product_list,500);
    foreach($product_list as $result){
      $q = Branch_product::insert($result);
    }

    if($q == true){
      return "true";
    }else{
      return "false";
    }
  }

  public function editBranch(Request $request)
  {
    if($request->method == "get"){

      $branch = Branch::where('id',$request->id)->first();

      return $branch;

    }else if($request->method == "postBranch"){

      $result = Branch::where('id',$request->id)
                        ->update([
                          'branch_name' => $request->name,
                          'address' => $request->address,
                          'contact' => $request->contact
                        ]);

      return $result;
    }

    
  }

  public function getBranchStockList(Request $request)
  {
    $url = route('home')."?p=branch_menu";

    $branch = Branch::get();
    $branch_product = Branch_product::join('department','department.id','=','branch_product.department_id')
                                      ->join('category','category.id','=','branch_product.category_id')
                                      ->where('branch_product.branch_id',$request->branch_id)
                                      ->select('branch_product.*','department.department_name','category.category_name')
                                      ->paginate(25);

    foreach($branch as $key => $result){
      $branch[$key]->url = route('getBranchStockList',$result->id);
    }

    $branch_id = $request->branch_id;

    return view('branch_stock_list',compact('branch','branch_product','branch_id','url'));
  }

  public function searchBranchProduct(Request $request)
  {
    $url = route('home')."?p=branch_menu";

    $branch = Branch::get();
    $branch_product = Branch_product::where('branch_id',$request->branch_id)
                                    ->where(function($query) use ($request) {
                                      $query->where('barcode','LIKE',"%".$request->search."%");
                                      $query->orWhere('product_name','LIKE','%'.$request->search.'%');
                                    })
                                    ->paginate(25);

    $branch_product->withPath('?search='.$request->search.'&branch_id='.$request->branch_id);

    foreach($branch as $key => $result){
      $branch[$key]->url = route('getBranchStockList',$result->id);
    }

    $branch_id = $request->branch_id;

    return view('branch_stock_list',compact('branch','branch_product','branch_id','url'));                         
  }


  public function getModifyBranchStock(Request $request)
  {

    $url = route('getBranchStockList',$request->branch_id);

    $product = Branch_product::join('department','department.id','=','branch_product.department_id')
                              ->join('category','category.id','=','branch_product.category_id')
                              ->select('branch_product.*','department.department_name','category.category_name')
                              ->where('branch_product.id',$request->id)
                              ->first();

    $default_price = Product_configure::first();

    return view('branch_stock_edit',compact('product','default_price','url'));
  }

  public function postModifyBranchStock(Request $request)
  {
    Branch_product::where('id',$request->id)
                    ->update([
                      'reorder_level' => $request->reorder_level,
                      'recommend_quantity' => $request->recommend_quantity,
                      'quantity' => round($request->stock_quantity,3),
                      'price'=> $request->price,
                      'product_sync' => 0,
                    ]);

    return "success";
  }

  public function getBranchRestock()
  {
    $url = route('home')."?p=branch_menu";

    $branch = Branch::get();
    $branch_product = new \stdClass();
    $branch_id = null;

    if(isset($_GET['branch_id'])){
      $branch_product = Branch_product::whereRaw('reorder_level >= quantity')
                                      ->where('branch_id',$_GET['branch_id'])
                                      ->where('quantity','!=',null)
                                      ->get();
      if($branch_product->count() != 0){
        $branch_id = $_GET['branch_id'];
      }
    }

    return view('branch_restock',compact('branch','branch_product','branch_id','url'));
  }
  
  public function postBranchStock(Request $request)
  {
    $do_configure = Do_configure::first();
    $header = $do_configure->do_prefix_number.$do_configure->next_do_number;
    $do_number = intval($do_configure->next_do_number);
    $next = strval(++$do_number);
    $i=6;
    while($i>strlen($next)){
      $next = "0".$next;
    }
    Do_configure::where('id',1)->update(['current_do_number'=>$do_configure->next_do_number,'next_do_number'=>$next]);

    if($request->branch_transfer == "0"){
      $from = "HQ";
      $from_branch_id = 0;
    }else{
      $branch = Branch::where('id',$request->branch_transfer)->first();
      $from = $branch->branch_name;
      $from_branch_id = $request->branch_transfer;
    }

    $branch = Branch::where('id',$request->branch_id)->first();
    $to = $branch->branch_name;

    Do_list::create([
      'do_number' => $header,
      'from' => $from,
      'from_branch_id' => $from_branch_id,
      'to' => $to,
      'to_branch_id' => $request->branch_id,
      'total_item' => array_sum($request->reorder_quantity),
      'description' => $request->description,
      'completed' => 0,
    ]);

    for($i=0;$i<count($request->barcode);$i++){
      Do_detail::create([
        'do_number' => $header,
        'product_id' => $request->product_id[$i],
        'barcode' => $request->barcode[$i],
        'product_name' => $request->product_name[$i],
        'price' => $request->product_price[$i],
        'cost' => $request->product_cost[$i],
        'quantity' => $request->reorder_quantity[$i],
      ]);
    }

    //Deduct Stock From Selected Branch
    if($from_branch_id == 0){
      for($i=0;$i<count($request->barcode);$i++){
        Warehouse_stock::where('barcode',$request->barcode[$i])
                          ->update([
                            'quantity' => DB::raw('IF (quantity IS null,0,quantity) -'.$request->reorder_quantity[$i]),
                          ]);
      }
      
    }else{
      for($i=0;$i<count($request->barcode);$i++){
        Branch_product::where('branch_id',$from_branch_id)
                        ->where('barcode',$request->barcode[$i])
                        ->update([
                          'quantity' => DB::raw('IF (quantity IS null,0,quantity) -'.$request->reorder_quantity[$i]),
                        ]);
      }
    }

    return redirect(route('getPrintDo',$header));  
  }


  public function getPrintDo(Request $request)
  {
    $url = route('home')."?p=branch_menu";
    $do_list = Do_list::where('do_number',$request->do_number)->first();
    $do_detail = Do_detail::where('do_number',$request->do_number)->get();

    return view('print_do',compact('do_list','do_detail','url'));
  }

  public function getDoHistory()
  {
    $url = route('home')."?p=branch_menu";

    if(isset($_GET['search']) && $_GET['search'] != null){
      $do_list = Do_list::join('do_detail as dd','dd.do_number','=','do_list.do_number')
                          ->where('do_list.do_number','%'.$_GET['search'].'%')
                          ->where('do_list.completed',0)
                          ->selectRaw("do_list.*,SUM(do_detail.price * do_detail.quantity) as total_value")
                          ->orderBy('do_list.created_at','desc')
                          ->groupBy('do_list.do_number')
                          ->paginate(15);
    }else{
      $do_list = Do_list::join('do_detail as dd','dd.do_number','=','do_list.do_number')
                          ->selectRaw("do_list.*,SUM(dd.price * dd.quantity) as total_value")
                          ->where('do_list.completed',0)
                          ->orderBy('do_list.created_at','desc')
                          ->groupBy('do_list.do_number')
                          ->paginate(15);
    }

    return view('do_history',compact('do_list','url'));
  }

  public function postDeleteDo(Request $request)
  {
    $user = Auth::user();
    $do = Do_list::where('id',$request->id)->first();
    $list = Do_detail::where('do_number',$do->do_number)->get();

    if($do->from_branch_id == 0){
      foreach($list as $result){
        Warehouse_stock::where('barcode',$result->barcode)
                        ->update([
                          'quantity'=>DB::raw('IF (quantity IS null,0,quantity) +'.$result->quantity),
                        ]);
      }
    }else{
      foreach($list as $result){
        Branch_product::where('branch_id',$do->from_branch_id)
                        ->where('barcode',$result->barcode)
                        ->update([
                          'quantity' => DB::raw('IF (quantity IS null,0,quantity) +'.$result->quantity),
                        ]);
      }
    }

    Do_list::where('id',$request->id)->update(['deleted_by'=>$user->name]);
    Do_list::where('id',$request->id)->delete();

    return json_encode(true);
  }

  public function getDoHistoryDetail(Request $request)
  {
    $url = route('getDoHistory');
    $do_list = Do_list::where('do_number',$request->do_number)->first(); 
    $do_detail = Do_detail::where('do_number',$request->do_number)->get();

    $stock_lost_quantity = 0;
    $total_lost_amount = 0;
    foreach($do_detail as $result){
      $stock_lost_quantity += $result->stock_lost_quantity;
      $total_lost_amount += ($result->stock_lost_quantity * $result->price);
    }

    return view('do_history_detail',compact('do_list','do_detail','stock_lost_quantity','total_lost_amount','url'));
  }

  public function getRestocklist()
  {
    $url = route('home')."?p=branch_menu";

    if(isset($_GET['search']) && $_GET['search'] != null){
      $do_list = Do_list::where('do_number',$_GET['search'])
                          ->where('completed','0')
                          ->orderBy('created_at','desc')->paginate(15);
    }else{
      $do_list = Do_list::where('completed','0')->orderBy('created_at','desc')->paginate(15);
    }

    return view('restock_list',compact('do_list','url'));
  }

  public function getRestockConfirmation(Request $request)
  { 
    $url = route('home')."?p=branch_menu";

    $do_list = Do_list::where('do_number',$request->do_number)->first(); 
    $do_detail = Do_detail::where('do_number',$request->do_number)->get();

    return view('restock_confirmation',compact('do_list','do_detail','url'));
  }

  public function postRestockConfirmation(Request $request)
  {
    //stock lost status, 0 = no stock lost, 1 = gt stock lost
    $stock_lost_status = 0;
    $stock_lost_review = 0;
    for($x=0;$x<count($request->do_detail_id);$x++){

      if($request->stock_lost_quantity[$x] > 0){
        $stock_lost_status = 1;
        $stock_lost_review = 1;
        $stock_lost_reason = $request->stock_lost_reason[$x];
        $stock_lost_quantity = $request->stock_lost_quantity[$x];
      }else{
        $stock_lost_reason = null;
        $stock_lost_quantity = 0;
        $stock_lost_review = 0;
      }

      if($request->restock_quantity[$x] > 0){
        $restock_quantity = $request->restock_quantity[$x];
      }else{
        $restock_quantity = 0;
      }

      $do_list = Do_list::where('do_number',$request->do_number)->first();
      $do_detail = Do_detail::where('id',$request->do_detail_id[$x])
                              ->update([
                                'stock_lost_quantity' => $stock_lost_quantity,
                                'stock_lost_reason' => $stock_lost_reason,
                                'remark' => $request->remark[$x],
                                'stock_lost_review' => $stock_lost_review,
                              ]);

      //Decide to take branch product or warehouse product                        
      if($do_list->to == "HQ"){
        $warehouse_stock = Warehouse_stock::where('id',$request->product_id[$x])->first();
        if($warehouse_stock != null){
          $total_restock_quantity = $restock_quantity + intval($warehouse_stock->quantity);
          Warehouse_stock::where('id',$request->product_id[$x])
                  ->update([
                    'quantity' => $total_restock_quantity,
                  ]);
        }
      }else{  
        $branch_product = Branch_product::where('id',$request->product_id[$x])->first();
        if($branch_product != null){
          $total_restock_quantity = $restock_quantity + intval($branch_product->quantity);
          Branch_product::where('id',$request->product_id[$x])
                  ->update([
                    'quantity' => $total_restock_quantity,
                  ]);
        }
      }

    }         
    
    Do_list::where('do_number',$request->do_number)
              ->update([
                'completed' => 1,
                'completed_time' => now(),
                'stock_lost' => $stock_lost_status,
              ]);
    
    return redirect(route('getRestocklist'))->with('success','Update Successful');      
  }

  public function getRestockHistory()
  {
    $url = route('home')."?p=branch_menu";

    if(isset($_GET['search']) && $_GET['search'] != null){
      $do_list = Do_list::join('do_detail as dd','dd.do_number','=','do_list.do_number')
                          ->selectRaw("do_list.*,SUM(dd.price * dd.quantity) as total_value")
                          ->where('do_list.completed',1)
                          ->where('dd.do_number',$_GET['search'])
                          ->orderBy('do_list.created_at','desc')
                          ->groupBy('do_list.do_number')
                          ->paginate(15);
    }else{
      $do_list = Do_list::join('do_detail as dd','dd.do_number','=','do_list.do_number')
                          ->selectRaw("do_list.*,SUM(dd.price * dd.quantity) as total_value")
                          ->where('do_list.completed',1)
                          ->orderBy('do_list.created_at','desc')
                          ->groupBy('do_list.do_number')
                          ->paginate(15);
    }

    return view('restock_history',compact('do_list','url'));
  }

  public function getRestockHistoryDetail(Request $request)
  {
    $url = route('home')."?p=branch_menu";
    $do_list = Do_list::where('id',$request->id)->first();
    $do_detail = Do_detail::where('do_number',$do_list->do_number)->get();

    return view('restock_history_detail',compact('do_list','do_detail','url'));
  }

  public function getDamagedStock()
  {
    $access = app('App\Http\Controllers\UserController')->checkAccessControl();
    if(!$access)
    {
      return view('not_access');
    }

    $url = route('home')."?p=branch_menu";

    $supplier = Supplier::get();

    $do_detail = Do_detail::where('stock_lost_reason','damaged')
                          ->where('stock_lost_review',1)
                          ->paginate(15);

    return view('damaged_stock_list',compact('do_detail','url','supplier'));
  }

  public function postDamagedStock(Request $request)
  {
    $response = new \stdClass();

    if($request->result == 'true'){
      $dmg_stock = Do_detail::where('stock_lost_review','1')
                              ->where('stock_lost_reason','damaged')
                              ->get();

      Do_detail::where('stock_lost_review','1')->where('stock_lost_reason','damaged')->update(['stock_lost_review' => '0']);

      $date = strtotime(date("Y-m-d h:i:s"));
      $key = "GR".$date;
      try{                     
        foreach($dmg_stock as $result){
          Damaged_stock_history::create([
            'gr_number' => $key,
            'do_number' => $result->do_number,
            'supplier_id' => $request->supplier_id,
            'barcode' => $result->barcode,
            'product_name' => $result->product_name,
            'lost_quantity' => $result->stock_lost_quantity,
            'price_per_unit' => $result->price,
            'total' => $result->price * $result->stock_lost_quantity,
            'remark' => $result->remark,
          ]);
        }

        $response->redirect = route('getGenerateGR',$key);
      }catch(Throwable $e){
        $response->redirect = null;
      }
    }else{
      $response->redirect = null;
    }

    return response()->json($response);
  }

  public function getGenerateGR(Request $request)
  {
    $url = route('home')."?p=branch_menu";

    $gr = Damaged_stock_history::where('gr_number',$request->gr_number)->get();

    $supplier = Supplier::where('id',$gr[0]->supplier_id)->first();

    $total = new \stdClass();
    $a=0;
    $q=0;
    foreach($gr as $result){
      $a += floatval($result->total);
      $q += intval($result->lost_quantity);
    }

    $total->quantity = $q;
    $total->amount = $a;

    return view('report_gr',compact('gr','supplier','total','url'));

  } 

  public function getDamagedStockHistory()
  {
    $url = route('getDamagedStock');

    $gr_list = Damaged_stock_history::select(DB::raw('SUM(total) as total,SUM(lost_quantity) as lost_quantity,gr_number,created_at'))
                                      ->groupBy('gr_number')
                                      ->orderBy('created_at','desc')
                                      ->paginate(20);

    return view('gr_history',compact('url','gr_list'));
  }

  public function getStockLost()
  {
    $access = app('App\Http\Controllers\UserController')->checkAccessControl();
    if(!$access)
    {
      return view('not_access');
    }
    
    $url = route('home')."?p=branch_menu";

    $do_detail = Do_detail::where('stock_lost_review',1)
                          ->where(function($query){
                            $query->where('stock_lost_reason','lost')
                                  ->orWhere('stock_lost_reason','other');
                          })
                          ->paginate(15);

    return view('stock_lost_list',compact('url','do_detail'));
  }

  public function postStockLost(Request $request)
  {
    $response = new \stdClass();

    if($request->result == 'true'){
      $stock_lost = Do_detail::where('stock_lost_review',1)
                          ->where(function($query){
                            $query->where('stock_lost_reason','lost')
                                  ->orWhere('stock_lost_reason','other');
                          })
                          ->get();

      Do_detail::where('stock_lost_review','1')
                ->where(function($query){
                  $query->where('stock_lost_reason','lost')
                        ->orWhere('stock_lost_reason','other');
                })
                ->update(['stock_lost_review' => '0']);

      $date = strtotime(date("Y-m-d h:i:s"));
      $key = "SL".$date;
      try{                     
        foreach($stock_lost as $result){
          Stock_lost_history::create([
            'stock_lost_id' => $key,
            'do_number' => $result->do_number,
            'barcode' => $result->barcode,
            'product_name' => $result->product_name,
            'lost_quantity' => $result->stock_lost_quantity,
            'price_per_unit' => $result->price,
            'cost' => $result->cost,
            'total' => $result->price * $result->stock_lost_quantity,
            'remark' => $result->remark,
          ]);
        }

        $response->redirect = route('getGenerateSL',$key);
      }catch(Throwable $e){
        $response->redirect = null;
      }
    }else{
      $response->redirect = null;
    }

    return response()->json($response);
  }

  public function getGenerateSL(Request $request)
  {

    $sl = Stock_lost_history::where('stock_lost_id',$request->sl_id)->get();

    $total = new \stdClass();
    $a=0;
    $q=0;
    foreach($sl as $result){
      $a += floatval($result->total);
      $q += intval($result->lost_quantity);
    }

    $total->quantity = $q;
    $total->amount = $a;

    return view('report_sl',compact('sl','total'));
  }

  public function getStockLostHistory()
  {
    $url = route('getStockLost');

    $sl_list = Stock_lost_history::select(DB::raw('SUM(total) as total,SUM(lost_quantity) as lost_quantity,stock_lost_id,created_at'))
                                      ->groupBy('stock_lost_id')
                                      ->orderBy('created_at','desc')
                                      ->paginate(20);

    return view('sl_history',compact('url','sl_list'));
  }

  public function getBranchStockHistory()
  {
    $url = route('home')."?p=branch_menu";

    $selected_date_from = date('Y-m-d', strtotime(now()));
    $selected_date_to = date('Y-m-d', strtotime(now()));
    
    $branch = Branch::get();

    return view('branch.branch_stock_history',compact('branch', 'selected_date_from', 'selected_date_to','url'));
  }

  public function getBranchStockHistoryDetail(Request $request)
  {
    $url = route('home')."?p=branch_menu";

    $selected_branch = null;
    $selected_branch_token = null;
    $selected_date_from = date('Y-m-d', strtotime(now()));
    $selected_date_to = date('Y-m-d', strtotime(now()));

    $date = date('Y-m-d H:i:s', strtotime(now()));
    $user = Auth::user();

    // default
    if($request->branch_token)
    {
      $selected_branch_token = $request->branch_token;
    }

    if($request->report_date_from)
    {
      $selected_date_from = $request->report_date_from;
    }

    if($request->report_date_to)
    {
      $selected_date_to = $request->report_date_to;
    }

    if(!$selected_branch_token)
    {
      $selected_branch = Branch::first();
    }
    else
    {
      $selected_branch = Branch::where('token', $selected_branch_token)->first();
    }

    $selected_date_start = $selected_date_from." 00:00:00";
    $selected_date_end = $selected_date_to." 23:59:59";

    if($selected_branch)
    {
      $branch_stock_history = Branch_stock_history::where('stock_type', 'branch')->whereBetween('created_at', [$selected_date_start, $selected_date_end])->where('branch_token', $selected_branch->token)->orderBy('created_at')->get();
    }
    else
    {
      $branch_stock_history = Branch_stock_history::where('stock_type', 'branch')->whereBetween('created_at', [$selected_date_start, $selected_date_end])->where('branch_token', null)->orderBy('created_at')->get();
    }

    return view('branch/branch_stock_history_detail',compact('selected_branch', 'selected_date_from', 'selected_date_to', 'branch_stock_history', 'url', 'date', 'user'));
  }

  public function getManualStockOrder()
  {
    $url = route('home')."?p=branch_menu";

    $branch_product = new \stdClass();
    $target = new \stdClass();

    if(isset($_GET['search'])){
      if($_GET['branch_id'] != 'hq'){
        $target = Branch_product::where('branch_id',$_GET['branch_id'])
                                  ->where('barcode',$_GET['search'])
                                  ->first();

        $branch_product = Branch_product::where('branch_id',$_GET['branch_id'])
                                  ->where('barcode','!=',$_GET['search'])
                                  ->where('barcode','LIKE','%'.$_GET['search'].'%')
                                  ->orWhere(function($query){
                                      $query->where('product_name','LIKE','%'.$_GET['search'].'%')
                                            ->where('branch_id',$_GET['branch_id'])
                                            ->where('barcode','!=',$_GET['search']);
                                  })
                                  ->paginate(15);

      }else{
        $target = Warehouse_stock::where('barcode',$_GET['search'])
                                  ->first();

        $branch_product = Warehouse_stock::where('barcode','!=',$_GET['search'])
                                          ->where('barcode','LIKE','%'.$_GET['search'].'%')
                                           ->orWhere(function($query){
                                              $query->where('product_name','LIKE','%'.$_GET['search'].'%')
                                                    ->where('barcode','!=',$_GET['search']);
                                          })
                                          ->paginate(15);
      }

    }else{
      if($_GET['branch_id'] != 'hq'){
        $branch_product = Branch_product::where('branch_id',$_GET['branch_id'])
                                      ->orderBy('updated_at','desc')
                                      ->paginate(15);
      }else{
        $branch_product = Warehouse_stock::orderBy('updated_at','desc')
                                          ->paginate(15);
      }
    }


    $branch = Branch::get();
    $from = $_GET['from'];
    $branch_id = $_GET['branch_id'];
    $search = (isset($_GET['search'])) ? $_GET['search'] : null;
    $page = isset($_GET['page']) ? $_GET['page'] : null;

    return view('manual_stock_order',compact('url','branch_product','branch','from','branch_id','target','search','page'));
  }

  public function ajaxAddManualStockOrder(Request $request)
  {
    if($request->to == "hq"){
      $product_detail = Warehouse_stock::where('id',$request->branch_product_id)->first();
    }else{
      $product_detail = Branch_product::where('id',$request->branch_product_id)->first();
    }

    try{
      $result = Tmp_order_list::updateOrCreate(
                              [
                                'from_branch' => $request->from,
                                'to_branch' => $request->to,
                                'branch_product_id' => $product_detail->id,
                                'user_id' => Auth::user()->id,
                              ]
                              ,[
                                'department_id' => $product_detail->department_id,
                                'category_id' => $product_detail->category_id,
                                'barcode' => $product_detail->barcode,
                                'product_name' => $product_detail->product_name,
                                'measurement' => $product_detail->measurement,
                                'cost' => $product_detail->cost,
                                'price' => $product_detail->price,
                                'order_quantity' => $request->order_quantity,
                              ]);
      return "true";

    }catch(Throwable $e){
      return $e;
    }
  }

  public function getManualOrderList()
  {
    $user = Auth::user()->id;
    $branch = Branch::first();
    $url = route('getManualStockOrder')."?branch_id=0&from=0";
    $from = new \stdClass();
    $to = new \stdClass();

    $branch_group = Tmp_order_list::groupBy(['to_branch','from_branch'])
                                    ->where('to_branch',$_GET['id'])
                                    ->where('user_id',$user)
                                    ->first();

    if($branch_group == null){ 
      $branch = Branch::first();
      return "<script>
              alert('No order data, you will be redirect to order page shortly');
              window.location.assign('".route('getManualStockOrder')."?branch_id=0&from=0');
              </script>";
    }

    if($branch_group->from_branch == 0)
      $from->branch_name = "HQ";
    else
      $from = Branch::where('id',$branch_group->from_branch)->select('branch_name')->first();

    if($branch_group->to_branch == 0)
      $to->branch_name = "HQ";
    else  
      $to = Branch::where('id',$branch_group->to_branch)->select('branch_name')->first();

    $tmp = Tmp_order_list::where('from_branch',$branch_group->from_branch)
                          ->where('to_branch',$branch_group->to_branch)
                          ->where('user_id',$user)
                          ->get();
    $total_item = 0;
    foreach($tmp as $result){
      $total_item += $result->order_quantity; 
    }

    return view('manual_order_list',compact('url','tmp','branch','from','to','total_item'));
  }

  public function postManualOrderList(Request $request)
  {
    $user = Auth::user()->id;
    $do_configure = Do_configure::first();
    $header = $do_configure->do_prefix_number.$do_configure->next_do_number;
    $do_number = intval($do_configure->next_do_number);
    $next = strval(++$do_number);
    $i=6;
    while($i>strlen($next)){
      $next = "0".$next;
    }
    Do_configure::where('id',1)->update(['current_do_number'=>$do_configure->next_do_number,'next_do_number'=>$next]);

    Do_list::create([
      'do_number' => $header,
      'from' => $request->from,
      'from_branch_id' => $request->from_branch_id,
      'to' => $request->to,
      'to_branch_id' => $request->to_branch_id,
      'total_item' => array_sum($request->order_quantity),
      'description' => "",
      'completed' => 0,
      'user_id'=> $user,
    ]);

    for($a=0;$a < count($request->barcode);$a++){
      Do_detail::create([
        'do_number' => $header,
        'product_id' => $request->product_id[$a],
        'barcode' => $request->barcode[$a],
        'product_name' => $request->product_name[$a],
        'measurement' => $request->measurement[$a],
        'price' => $request->price[$a],
        'cost' => $request->cost[$a],
        'quantity' => $request->order_quantity[$a],
      ]);
    }

    if($request->from_branch_id == 0){
      for($i=0;$i<count($request->barcode);$i++){
        Warehouse_stock::where('barcode',$request->barcode[$i])
                          ->update([
                            'quantity' => DB::raw('IF (quantity IS null,0,quantity) -'.$request->order_quantity[$i]),
                          ]);
      }
      
    }else{
      for($i=0;$i<count($request->barcode);$i++){
        Branch_product::where('branch_id',$request->from_branch_id)
                        ->where('barcode',$request->barcode[$i])
                        ->update([
                          'quantity' => DB::raw('IF (quantity IS null,0,quantity) -'.$request->order_quantity[$i]),
                        ]);
      }
    }

    //Delete item from Tmp_order_list

    Tmp_order_list::where('from_branch',$request->from_branch_id)
                    ->where('to_branch',$request->to_branch_id)
                    ->where('user_id',$user)
                    ->delete();

    return "true";
  }

  public function ajaxRemoveItem(Request $request)
  {
    try{

      Tmp_order_list::where('id',$request->id)->delete();

      return "true";

    }catch(Throwable $e){

      return "false";
    }
  }

  public function getStockAdjustment()
  {
    $url = route('home')."?p=branch_menu";

    $branches = Branch::all();

    return view('branch.stock_adjustment',compact('url','branches'));
  }

  public function postExportBranchStock(Request $request)
  {
    if(!Storage::exists('public/exportList'))
    {
      Storage::makeDirectory('public/exportList', 0775, true); //creates directory
    }

    $files = Storage::allFiles('public/exportList');
    Storage::delete($files);

    $branchStock = Branch_product::join('category','category.id','=','branch_product.category_id')
                                  ->join('department','department.id','=','branch_product.department_id')
                                  ->where('branch_product.branch_id',$request->branch_id)
                                  ->orderBy('branch_product.id','ASC')
                                  ->get();

    $spreadsheet = new Spreadsheet();
    $spreadsheet->getActiveSheet()->getProtection()->setSheet(true);
    $spreadsheet->getDefaultStyle()->getProtection()->setLocked(false);
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->getStyle('A')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_PROTECTED);
    $sheet->getStyle('A')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('000');
    $sheet->getStyle('B')->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_PROTECTED);
    $sheet->getStyle('B')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('72d4b2');
    $sheet->getStyle('B')->getAlignment()->setHorizontal('left');
    $sheet->setCellValue('A1','Branch Id' );
    $sheet->setCellValue('B1','Barcode' );
    $sheet->setCellValue('C1','Product Name' );
    $sheet->setCellValue('D1','Price' );
    $sheet->setCellValue('E1','Department' );
    $sheet->setCellValue('F1','Category' );
    $sheet->setCellValue('G1','Current Stock' );
    $sheet->setCellValue('H1','Updated Stock' );

    $row = 2;
    foreach($branchStock as $result){
      $sheet->getCellByColumnAndRow(1, $row)->setValue($request->branch_id); 
      $sheet->getCellByColumnAndRow(2, $row)->setValue($result->barcode); 
      $sheet->getCellByColumnAndRow(3, $row)->setValue($result->product_name); 
      $sheet->getCellByColumnAndRow(4, $row)->setValue($result->price); 
      $sheet->getCellByColumnAndRow(5, $row)->setValue($result->department_name); 
      $sheet->getCellByColumnAndRow(6, $row)->setValue($result->category_name); 
      $sheet->getCellByColumnAndRow(7, $row)->setValue($result->quantity); 
      $row++;
    }

    $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(47);
    $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(10);
    $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(17);
    $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(17);

    $time = date('d-M-Y h i s A');
    $writer = new Xlsx($spreadsheet);
    $path = 'storage/exportList/Branch Stock List ('.$time.').xlsx';
    $writer->save($path);

    return response()->json($path);
  }

  public function postImportBranchStock(Request $request)
  {
    if(!Storage::exists('public/importList'))
    {
      Storage::makeDirectory('public/importList', 0775, true); //creates directory
    }

    if($request->hasFile('branch_stock_list')){
      $path = Storage::url($request->file('branch_stock_list')->store('public/importList'));
    }

    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::load('./'.$path);
    $spreadsheet = $reader->getActiveSheet();

    $total_record = $spreadsheet->getHighestRow() - 1;

    for($i=1;$i<=$total_record;$i++){
      $n = $i+1;
      $branch_id = $spreadsheet->getCell('A2')->getValue();
      $barcode = $spreadsheet->getCell('B'.$n)->getValue();
      $quantity = $spreadsheet->getCell('H'.$n)->getValue();
      if(!empty($quantity)){
        Branch_product::where('branch_id',$branch_id)->where('barcode','LIKE',$barcode)->update(['quantity' => $quantity]);
      }
    }
    
    return back()->with('result','true');
  }

  public function ajaxRestockExcel(Request $request)
  {
    $branch_restock = Branch_product::where('branch_id',$request->branch_id)
                                      ->whereRaw('reorder_level >= quantity')
                                      ->get();
    
    //Start exporting
    $files = Storage::allFiles('public/report');
    Storage::delete($files);                  
    if(!Storage::exists('public/report'))
    {
    Storage::makeDirectory('public/report', 0775, true); //creates directory
    }                                  

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1','Barcode');
    $sheet->setCellValue('B1','Product Name');
    $sheet->setCellValue('C1','Current Quantity');
    $sheet->setCellValue('D1','Reorder Level');
    $sheet->setCellValue('E1','Recommended Quantity');
    $sheet->setCellValue('F1','Restock Quantity');


    $start = 2;
    foreach($branch_restock as $index => $result){
      $sheet->setCellValue('A'.$start, $result->barcode);
      $sheet->setCellValue('B'.$start, $result->product_name);
      $sheet->setCellValue('C'.$start, $result->quantity);
      $sheet->setCellValue('D'.$start, $result->reorder_level);
      $sheet->setCellValue('E'.$start, $result->recommend_quantity);
      $sheet->setCellValue('F'.$start, 0);
      $start++;
    }
    $spreadsheet->getActiveSheet()->getStyle('A')->getNumberFormat()->setFormatCode('################################');

    $date = strtotime("now");
    $writer = new Xlsx($spreadsheet);
    $path = 'storage/report/Restock Item List - '.$date.'.xlsx';
    $writer->save($path);

    return response()->json($path);
  }

}
