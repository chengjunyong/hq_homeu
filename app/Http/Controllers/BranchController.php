<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Branch;
use App\Branch_product;
use App\Product_list;
use App\transaction;
use App\transaction_detail;
use App\Product_configure;
use App\Do_detail;
use App\Do_configure;
use App\Do_list;

class BranchController extends Controller
{
  public function __construct()
  {
      $this->middleware('auth', ['except' => ['branchSync', 'branchSyncCompleted']]);
  }

  public function getBranch()
  {
   $branch = Branch::paginate(6);

   return view('branch',compact('branch'));
 }

  public function createBranch(Request $request)
  {

    $branch_id = Branch::create([
      'branch_name' => $request->branch_name,
      'address' => $request->address,
      'contact' => $request->contact_number,
      'token' => $request->token
    ]);

    $product_list = Product_list::select('department_id','category_id','barcode','product_name','cost','price','quantity','reorder_level','recommend_quantity','unit_type')
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

  public function getBranchStockList(Request $request)
  {
    $branch = Branch::get();
    $branch_product = Branch_product::where('branch_id',$request->branch_id)->paginate(25);
    foreach($branch as $key => $result){
      $branch[$key]->url = route('getBranchStockList',$result->id);
    }
    $branch_id = $request->branch_id;

    return view('branch_stock_list',compact('branch','branch_product','branch_id'));
  }

  public function searchBranchProduct(Request $request)
  {
    $branch = Branch::get();
    $branch_product = Branch_product::where('branch_id',$request->branch_id)
                                    ->where(function($query) use ($request) {
                                      $query->where('barcode','LIKE','%'.$request->search.'%')
                                            ->orWhere('product_name','LIKE','%'.$request->search.'%');
                                    })
                                    ->paginate(25);

    $branch_product->withPath('?search='.$request->search.'&branch_id='.$request->branch_id);

    foreach($branch as $key => $result){
      $branch[$key]->url = route('getBranchStockList',$result->id);
    }

    $branch_id = $request->branch_id;

    return view('branch_stock_list',compact('branch','branch_product','branch_id'));                         
  }


  public function getModifyBranchStock(Request $request)
  {
    $product = Branch_product::join('department','department.id','=','branch_product.department_id')
                              ->join('category','category.id','=','Branch_product.category_id')
                              ->select('branch_product.*','department.department_name','category.category_name')
                              ->where('branch_product.id',$request->id)
                              ->first();

    $default_price = Product_configure::first();

    return view('branch_stock_edit',compact('product','default_price'));
  }

  public function postModifyBranchStock(Request $request)
  {
    Branch_product::where('id',$request->id)
                    ->update([
                      'reorder_level' => $request->reorder_level,
                      'recommend_quantity' => $request->recommend_quantity,
                      'quantity' => $request->stock_quantity,
                    ]);

    return "success";
  }

  public function getBranchRestock()
  {
    $branch = Branch::get();
    $branch_product = new \stdClass();
    $branch_id = null;

    if(isset($_GET['branch_id'])){
      $branch_product = Branch_product::where('reorder_level','>=','quantity')
                                      ->where('branch_id',$_GET['branch_id'])
                                      ->get();
      if($branch_product->count() != 0){
        $branch_id = $_GET['branch_id'];
      }
    }

    return view('branch_restock',compact('branch','branch_product','branch_id'));
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
    }else{
      $branch = Branch::where('id',$request->branch_transfer)->first();
      $from = $branch->branch_name;
    }

    $branch = Branch::where('id',$request->branch_id)->first();
    $to = $branch->branch_name;

    Do_list::create([
      'do_number' => $header,
      'from' => $from,
      'from_branch_id' => $request->branch_transfer,
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
        'quantity' => $request->reorder_quantity[$i],
      ]);
    }

    return redirect(route('getPrintDo',$header));  
  }


  public function getPrintDo(Request $request)
  {
    $do_list = Do_list::where('do_number',$request->do_number)->first();
    $do_detail = Do_detail::where('do_number',$request->do_number)->get();

    return view('print_do',compact('do_list','do_detail'));
  }

  public function getDoHistory()
  {
    if(isset($_GET['search']) && $_GET['search'] != null){
      $do_list = Do_list::where('do_number',$_GET['search'])->orderBy('created_at','desc')->paginate(15);
    }else{
      $do_list = Do_list::orderBy('created_at','desc')->paginate(15);
    }

    return view('do_history',compact('do_list'));
  }

  public function getDoHistoryDetail(Request $request)
  {
    $do_list = Do_list::where('do_number',$request->do_number)->first(); 
    $do_detail = Do_detail::where('do_number',$request->do_number)->get();

    return view('do_history_detail',compact('do_list','do_detail'));
  }

  public function getRestocklist()
  {
    if(isset($_GET['search']) && $_GET['search'] != null){
      $do_list = Do_list::where('do_number',$_GET['search'])
                          ->where('completed','0')
                          ->orderBy('created_at','desc')->paginate(15);
    }else{
      $do_list = Do_list::where('completed','0')->orderBy('created_at','desc')->paginate(15);
    }
    
    return view('restock_list',compact('do_list'));
  }

  public function getRestockConfirmation(Request $request)
  { 
    $do_list = Do_list::where('do_number',$request->do_number)->first(); 
    $do_detail = Do_detail::where('do_number',$request->do_number)->get();

    return view('restock_confirmation',compact('do_list','do_detail'));
  }

  public function postRestockConfirmation(Request $request)
  {

    dd($request);
  }

  public function branchSync(Request $request)
  {
    $transaction = $request->transaction;
    $transaction = json_decode($transaction, true);
    $transaction_detail = $request->transaction_detail;
    $transaction_detail = json_decode($transaction_detail, true);

    $branch_id = $request->branch_id;
    $session_id = $request->session_id;

    transaction::where('branch_id', $branch_id)->where('session_id', $session_id)->delete();
    transaction_detail::where('branch_id', $branch_id)->where('session_id', $session_id)->delete();

    $transaction_query = [];
    foreach($transaction as $data)
    {
      $query = [
        'branch_transaction_id' => $data['id'],
        'branch_id' => $branch_id,
        'session_id' => $session_id,
        'transaction_no' => $data['transaction_no'],
        'invoice_no' => $data['invoice_no'],
        'user_id' => $data['user_id'],
        'subtotal' => $data['subtotal'],
        'total_discount' => $data['total_discount'],
        'payment' => $data['payment'],
        'payment_type' => $data['payment_type'],
        'payment_type_text' => $data['payment_type_text'],
        'balance' => $data['balance'],
        'total' => $data['total'],
        'void' => $data['void'],
        'completed' => $data['completed'],
        'transaction_date' => $data['transaction_date'],
        'created_at' => $data['created_at'],
        'updated_at' => $data['updated_at']
      ];

      array_push($transaction_query, $query);
    }

    transaction::insert($transaction_query);

    $transaction_detail_query = [];
    foreach($transaction_detail as $data)
    {
      $query = [
        'branch_id' => $branch_id,
        'session_id' => $session_id,
        'branch_transaction_detail_id' => $data['id'],
        'branch_transaction_id' => $data['transaction_id'],
        'product_id' => $data['product_id'],
        'barcode' => $data['barcode'],
        'product_name' => $data['product_name'],
        'quantity' => $data['quantity'],
        'price' => $data['price'],
        'discount' => $data['discount'],
        'subtotal' => $data['subtotal'],
        'total' => $data['total'],
        'void' => $data['void'],
        'created_at' => $data['created_at'],
        'updated_at' => $data['updated_at']
      ];

      array_push($transaction_detail_query, $query);
    }

    transaction_detail::insert($transaction_detail_query);

    // more than 10000, php will return error
    $product_list = product_list::select('department_id', 'category_id', 'barcode', 'product_name', 'price')->where('product_sync', 0)->limit(10000)->get();

    $response = new \stdClass();
    $response->error = 0;
    $response->message = "Transaction sync completed";
    $response->product_list = $product_list->toJson();

    return response()->json($response);
  }

  public function branchSyncCompleted(Request $request)
  {
    $barcode_array = explode("|", $request->barcode_array);

    product_list::where('product_sync', 0)->whereIn('barcode', $barcode_array)->update([
      'product_sync' => 1
    ]);

    $response = new \stdClass();
    $response->error = 0;
    $response->message = "Product list sync completed";

    return response()->json($response);
  }

  public function getSalesReport(Request $request)
  {
    $branch = Branch::get();

    $selected_branch = null;
    $selected_branch_token = null;
    $selected_date_from = date('Y-m-d', strtotime(now()));
    $selected_date_to = date('Y-m-d', strtotime(now()));

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
      $transaction = transaction::whereBetween('transaction_date', [$selected_date_start, $selected_date_end])->where('branch_id', $selected_branch->token)->paginate(25);
    }
    else
    {
      $transaction = transaction::whereBetween('transaction_date', [$selected_date_start, $selected_date_end])->where('branch_id', null)->paginate(25);
    }

    return view('sales_report',compact('branch', 'selected_branch', 'selected_date_from', 'selected_date_to', 'transaction'));
  }

  public function getSalesReportDetail($branch_id, $branch_transaction_id)
  {
    $transaction_detail = transaction_detail::where('branch_id', $branch_id)->where('branch_transaction_id', $branch_transaction_id)->paginate(25);

    return view('sales_report_detail',compact('transaction_detail'));
  }
}
