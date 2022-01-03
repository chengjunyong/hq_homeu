<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Warehouse_stock;
use App\Department;
use App\Category;
use App\Supplier;
use App\Product_list;
use App\Branch_product;
use App\Purchase_order;
use App\Purchase_order_detail;
use App\Warehouse_restock_history;
use App\Warehouse_restock_history_detail;
use App\Branch_stock_history;
use App\Tmp_purchase_list;
use App\Tmp_invoice_purchase;
use App\Invoice_purchase;
use App\Invoice_purchase_detail;
use App\Good_return;
use App\Good_return_detail;
use App\Tmp_good_return;
use App\Write_off;
use App\Write_off_detail;
use Barryvdh\DomPDF\Facade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class WarehouseController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth', 'user_access']);
  }

  public function getWarehouseStockList()
  {
  	$url = route('home')."?p=stock_menu";
  	if(isset($_GET['search']) && $_GET['search'] != ""){
  		$search = 0;

      //Method 1
  		// $warehouse_stock = Warehouse_stock::where('product_name','LIKE','%'.$_GET['search'].'%')
    //                                       ->orWhere('barcode','LIKE','%'.$_GET['search'].'%')
  		// 																		->get();

      //Method 2
      $warehouse_stock = Warehouse_stock::where('barcode','LIKE',$_GET['search'])
                                          ->get();                                    
  	}else{
  		$warehouse_stock = Warehouse_stock::paginate(25);	
  		$search = 1;
  	}
  	

  	return view('warehousestocklist',compact('url','warehouse_stock','search'));
  }

  public function getAddWarehouseProduct()
  {
  	$url = route('getWarehouseStockList');
  	$department = Department::get();
  	$category = Category::where('department_id',$department->first()->id)->get();

  	return view('addwarehouseproduct',compact('url','department','category'));
  }

  public function postAddWarehouseProduct(Request $request)
  {

  	Warehouse_stock::create([
  		'department_id'				=>$request->department,
  		'category_id'					=>$request->category,
  		'barcode'							=>$request->barcode,
  		'product_name'  			=>$request->product_name,
  		'cost'								=>$request->cost,
  		'quantity'						=>0,
  		'reorder_level' 			=>$request->reorder_level,
  		'reorder_quantity' 	  =>$request->recommend_quantity,
  	]);

  	return back()->with('result','true');
  }

  public function getEditWarehouseProduct(Request $request)
  {
  	$url = route('getWarehouseStockList');

  	$warehouse_stock = Warehouse_stock::where('id',$request->id)->first();
  	$department = Department::get();
  	$category = Category::get();

  	return view('edit_warehouse_stock',compact('url','warehouse_stock','department','category'));
  }

  public function postModifyWarehouseProduct(Request $request)
  {
  	Warehouse_stock::where('barcode',$request->barcode)
  									->update([
                      'quantity' => $request->quantity,
  										'reorder_level' => $request->reorder_level,
  										'reorder_quantity' => $request->recommend_quantity,
  									]);

		return back()->with('result','Update Successful');  	
  }

  public function getPurchaseOrder()
  {
    $url = route('home')."?p=stock_menu";

    $supplier = Supplier::get();
    $items = Warehouse_stock::join('department','department.id','=','warehouse_stock.department_id')
                              ->join('category','category.id','=','warehouse_stock.category_id')
                              ->whereRaw('warehouse_stock.quantity <= warehouse_stock.reorder_level')
                              ->select('department.department_name','category.category_name','warehouse_stock.*')
                              ->get();

    return view('purchase_order',compact('url','supplier','items'));
  }

  public function ajaxGetSupplier(Request $request)
  {

    $supplier = Supplier::where('id',$request->id)->first();
    
    return $supplier;
  }

  public function ajaxPO(Request $request)
  {
    $product_list = Warehouse_stock::whereIn('id',$request->product_id)->get();

    $total_cost = 0;
    foreach($product_list as $key => $result){
      $total_cost += doubleval($result->cost) * intval($request->product_quantity[$key]);
    }

    $date = strtotime(date("Y-m-d h:i:s"));
    $po_number = "PO".$date;

    $purchase_order = Purchase_order::create([
                        'po_number' => $po_number,
                        'supplier_id' => $request->supplier_id,
                        'supplier_code' => $request->supplier_code,
                        'supplier_name' => $request->supplier_name,
                        'total_quantity_items' => array_sum($request->product_quantity),
                        'total_amount' => $total_cost,
                        'issue_date' => $request->issue_date,
                      ]);

    foreach($product_list as $key => $result){
      Purchase_order_detail::create([
        'po_id' => $purchase_order->id,
        'po_number' => $po_number,
        'product_id' => $result->id,
        'barcode' => $result->barcode,
        'product_name' => $result->product_name,
        'cost' => $result->cost,
        'quantity' => $request->product_quantity[$key],
        'received' => 0,
      ]);
    }

    $msg = new \stdClass();
    $msg->success = 1;
    $msg->url = route('getGeneratePurchaseOrder',$purchase_order->id);

    return json_encode($msg);
  }

  public function getGeneratePurchaseOrder(Request $request)
  {
    $po = Purchase_order::where('id',$request->id)->first();
    $supplier = Supplier::where('id',$po->supplier_id)->first();
    $po_detail = Purchase_order_detail::where('po_id',$request->id)->get();
    $total = 0;
    foreach($po_detail as $result){
      $total += floatval($result->cost) * floatval($result->quantity);
    }

    return view('print_po',compact('po','po_detail','total','supplier'));
  }

  public function getPurchaseOrderHistory(Request $request)
  {
    $url = route('home')."?p=stock_menu";

    if(isset($request->filter) && $request->filter == true){
      $path = "?filter=true";
      $query = Purchase_order::orderBy('created_at','desc');

      if($request->po_no != ''){
        $query->where('po_number','LIKE','%'.$request->po_no.'%');
        $path .= "&ref_no=".$request->ref_no;
      }else{
        $path .= "&ref_no=";
      }

      if($request->supplier != 'null'){
        $query->where('supplier_id',$request->supplier);
        $path .= "&supplier=".$request->supplier;
      }else{
        $path .= "&supplier=null";
      }

      if($request->date_start != ''){
        $query->where('created_at','>',$request->date_start);
        $path .= "&date_start=".$request->date_start;
      }else{
        $path .= "&date_start=";
      }

      if($request->date_end != ''){
        $query->where('created_at','<=',date("Y-m-d",strtotime($request->date_end."+1 day")));
        $path .= "&date_end=".$request->date_end;
      }else{
        $path .= "&start_end=";
      }

      $po = $query->paginate(15);
      $po->withPath($path);

    }else{

      $po = Purchase_order::orderBy('created_at','desc')->paginate(15);
    }

    $supplier = Supplier::get();

    return view('purchase_order_history',compact('url','po','supplier'));
  }

  public function getDeletePurchaseOrder(Request $request)
  {
    $user = Auth::user();
    Purchase_order::where('id',$request->id)->update(['deleted_by'=>$user->name]);
    Purchase_order::where('id',$request->id)->delete();

    return json_encode(true);
  }

  public function getPoHistoryDetail(Request $request)
  {
    $url = route('getPurchaseOrderHistory');

    $po_detail = Purchase_order::join('supplier','supplier.id','=','purchase_order.supplier_id')
                                ->where('purchase_order.po_number','=',$request->po_number)
                                ->select('purchase_order.*','supplier.supplier_name')
                                ->first();

    $po_list = Purchase_order_detail::where('po_number',$request->po_number)
                                      ->get();

    return view('po_history_detail',compact('url','po_detail','po_list'));
  }

  public function getPoList()
  {
    $url = route('home')."?p=stock_menu";

    $po = Purchase_order::where('completed','0')
                          ->orderBy('created_at','desc')
                          ->paginate(10);

    return view('po_list',compact('url','po'));
  }

  public function getWarehouseRestock(Request $request)
  {
    $url = route('getPoList');

    $po = Purchase_order::where('po_number',$request->po_number)
                          ->where('completed',0)
                          ->first();

    $po_detail = Purchase_order_detail::where('po_number',$request->po_number)->get();

    if($po == null){
      return redirect(route('getPoList'));
    }else{
      return view('warehouse_restock',compact('url','po_detail','po'));
    }

  }

  public function postWarehouseRestock(Request $request)
  {
    Purchase_order::where('id',$request->po_id)->update(['completed' => '1']);

    $warehouse =  Warehouse_restock_history::create([
                    'po_id' => $request->po_id,
                    'po_number' => $request->po_number,
                    'supplier_id' => $request->supplier_id,
                    'supplier_code' => $request->supplier_code,
                    'supplier_name' => $request->supplier_name,
                    'invoice_number' => $request->invoice_number,
                  ]);

    $id = $warehouse->id;

    foreach($request->product_id as $key => $result){
      Warehouse_restock_history_detail::create([
        'warehouse_history_id' => $id,
        'product_id' => $request->product_id[$key],
        'barcode' => $request->barcode[$key],
        'product_name' => $request->product_name[$key],
        'cost' => $request->cost[$key],
        'quantity' => $request->received_quantity[$key],
      ]);

      $warehouse = Warehouse_stock::where('id',$request->product_id[$key])->first();

      if($warehouse->quantity == null){
        Warehouse_stock::where('id',$request->product_id[$key])
                      ->update([
                        'quantity' => $request->received_quantity[$key],
                        'cost' => $request->cost[$key],
                      ]);
      }else{
        Warehouse_stock::where('id',$request->product_id[$key])
                      ->update([
                        'quantity' => DB::raw('IF (quantity IS null,0,quantity) +'.$request->received_quantity[$key]),
                        'cost' => $request->cost[$key],
                      ]);
      }
    }

    return redirect(route('getPoList'))->with('success','success');
  }

  public function getWarehouseRestockHistory()
  {
    $url = route('home')."?p=stock_menu";

    $history = Warehouse_restock_history::orderBy('created_at','desc')->paginate(10);

    return view('warehouse_restock_history',compact('url','history'));
  }

  public function getWarehouseRestockHistoryDetail(Request $request)
  {
    $url = route('getWarehouseRestockHistory');

    $po = Purchase_order::where('po_number',$request->po_number)->first();

    $invoice = Warehouse_restock_history::where('id',$request->id)->first();

    $detail = Warehouse_restock_history_detail::where('warehouse_history_id',$request->id)->get();

    return view('warehouse_restock_view',compact('url','invoice','detail','po'));
  }

  public function getWarehouseStockHistory()
  {
    $url = route('home')."?p=stock_menu";

    $selected_date_from = date('Y-m-d', strtotime(now()));
    $selected_date_to = date('Y-m-d', strtotime(now()));
  

    return view('warehouse.warehouse_stock_history',compact('selected_date_from', 'selected_date_to','url'));
  }

  public function getWarehouseStockHistoryDetail(Request $request)
  {
    $url = route('home')."?p=stock_menu";

    $selected_branch = null;
    $selected_branch_token = null;
    $selected_date_from = date('Y-m-d', strtotime(now()));
    $selected_date_to = date('Y-m-d', strtotime(now()));

    $date = date('Y-m-d H:i:s', strtotime(now()));
    $user = Auth::user();

    if($request->report_date_from)
    {
      $selected_date_from = $request->report_date_from;
    }

    if($request->report_date_to)
    {
      $selected_date_to = $request->report_date_to;
    }

    $selected_date_start = $selected_date_from." 00:00:00";
    $selected_date_end = $selected_date_to." 23:59:59";

    $warehouse_stock_history = Branch_stock_history::where('stock_type', 'warehouse')->whereBetween('created_at', [$selected_date_start, $selected_date_end])->orderBy('created_at')->get();

    return view('warehouse.warehouse_stock_history_detail',compact('selected_date_from', 'selected_date_to', 'warehouse_stock_history', 'url', 'date', 'user'));
  }

  public function getManualIssuePurchaseOrder()
  {
    $url = route('home')."?p=stock_menu";
    $target = new \stdClass();

    if(isset($_GET['search']) && $_GET['search'] != ""){
      $target = Warehouse_stock::where('barcode',$_GET['search'])->first();

      $warehouse_stock = Warehouse_stock::where('barcode','LIKE','%'.$_GET['search'].'%')
                                          ->where('barcode','!=',$_GET['search'])
                                          ->orWhere(function($query){
                                            $query->where('barcode','!=',$_GET['search'])
                                                  ->where('product_name','LIKE','%'.$_GET['search'].'%');
                                          })
                                          ->orderBy('updated_at','desc')
                                          ->paginate(20);

    }else{
      $warehouse_stock = Warehouse_stock::orderBy('updated_at','desc')
                                        ->paginate(20);
    }
    
    $supplier = Supplier::get();

    $page = isset($_GET['page']) ? $_GET['page'] : null;


    return view('warehouse.manual_issue_purchase_order',compact('url','warehouse_stock','supplier','target','page'));
  }

  public function ajaxAddManualStock(Request $request)
  { 
    $warehouse_stock = Warehouse_stock::where('id',$request->warehouse_stock_id)->first();
    $supplier = Supplier::where('id',$request->supplier_id)->first();
    $user = Auth::user()->id;

    try{
      if($request->foc == 'false'){
        $result = Tmp_purchase_list::create([
                                  'supplier_id'=>$request->supplier_id,
                                  'supplier_name' => $supplier->supplier_name,
                                  'warehouse_stock_id' => $warehouse_stock->id,
                                  'department_id' => $warehouse_stock->department_id,
                                  'category_id' => $warehouse_stock->category_id,
                                  'barcode' => $warehouse_stock->barcode,
                                  'product_name' => $warehouse_stock->product_name,
                                  'measurement' => $warehouse_stock->measurement,
                                  'cost' => $warehouse_stock->cost,
                                  'price' => $warehouse_stock->price,
                                  'order_quantity' => $request->order_quantity,
                                  'user_id'=>$user,
                                ]);
      }else{
        $result = Tmp_purchase_list::create([
                          'supplier_id'=>$request->supplier_id,
                          'supplier_name' => $supplier->supplier_name,
                          'warehouse_stock_id' => $warehouse_stock->id,
                          'department_id' => $warehouse_stock->department_id,
                          'category_id' => $warehouse_stock->category_id,
                          'barcode' => $warehouse_stock->barcode,
                          'product_name' => $warehouse_stock->product_name,
                          'measurement' => $warehouse_stock->measurement,
                          'cost' => 0,
                          'price' => 0,
                          'order_quantity' => $request->order_quantity,
                          'user_id'=>$user,
                        ]);
      }

      return "true";

    }catch(Throwable $e){
      return $e;
    }

  }

  public function getPurchaseOrderList()
  {
    $url = route('getManualIssuePurchaseOrder');
    $user = Auth::user()->id;
    $warehouse_group = Tmp_purchase_list::select(DB::raw('DISTINCT supplier_id'))
                                        ->where('user_id',$user)
                                        ->first();
    if($warehouse_group == null){ 
      return "<script>
              alert('No order data, you will be redirect to order page shortly');
              window.location.assign('".route('getManualIssuePurchaseOrder')."');
              </script>";
    }
    $supplier = Supplier::where('id',$warehouse_group->supplier_id)->first();
    $tmp = Tmp_purchase_list::where('supplier_id',$warehouse_group->supplier_id)
                            ->where('user_id',$user)
                            ->get();

    $total_item = 0;
    $total_cost = 0;
    foreach($tmp as $result){
      $total_item += $result->order_quantity; 
      $total_cost += doubleval($result->order_quantity) * doubleval($result->cost);
    }

    return view('warehouse.manual_purchase_order_list',compact('url','tmp','supplier','total_item','total_cost'));
  }

  public function ajaxRemovePurchaseOrderListItem(Request $request)
  {
    try{
      Tmp_purchase_list::where('id',$request->id)->delete();
      return "true";
    }catch(Throwable $e){
      return "false";
    }
  }

  public function ajaxUpdatePurchaseOrderListItem(Request $request)
  {
    try{
      Tmp_purchase_list::where('id',$request->id)->update(['cost'=>$request->cost]);
      return "true";
    }catch(Throwable $e){
      return "false";
    }

  }

  public function postManualPurchaseOrderList(Request $request)
  {
    $warehouse_stock = Warehouse_stock::whereIn('id',$request->product_id)->get();
    $supplier = Supplier::where('id',$request->supplier_id)->first();
    $date = strtotime(date("Y-m-d h:i:s"));
    $po_number = "PO".$date;
    $user = Auth::user()->id;

    $purchase_order = Purchase_order::create([
                        'po_number' => $po_number,
                        'supplier_id' => $supplier->id,
                        'supplier_code' => $supplier->supplier_code,
                        'supplier_name' => $supplier->supplier_name,
                        'total_quantity_items' => array_sum($request->order_quantity),
                        'total_amount' => $request->total_cost,
                        'issue_date' => $request->issue_date,
                        'user_id' => $user,
                      ]);

    foreach($request->product_id as $index => $result){
      Purchase_order_detail::create([
        'po_id' => $purchase_order->id,
        'po_number' => $po_number,
        'product_id' => $result,
        'barcode' => $request->barcode[$index],
        'product_name' => $request->product_name[$index],
        'measurement' => $request->measurement[$index],
        'cost' => $request->cost[$index],
        'quantity' => $request->order_quantity[$index],
        'received' => 0,
      ]);
    }

    //Delete item from Tmp_order_list
    Tmp_purchase_list::where('supplier_id',$request->supplier_id)
                      ->where('user_id',$user)
                      ->delete();

    return json_encode(true);
  }

  public function getStockPurchase()
  {
    $user = Auth::user()->id;
    $url = route('home')."?p=stock_menu";
    $a = Invoice_purchase::withTrashed()
                          ->whereRaw('YEAR(created_at) = '.date("Y"))
                          ->select('id')
                          ->orderBy('id','desc')
                          ->first();
    if(!$a){
      $last_id = 1;
    }else{
      $last_id = intval($a->id) + 1;
    }
    $i=7;
    while($i>strlen($last_id)){
      $last_id = "0".$last_id;
    }
    $reference_no = "P".$last_id;
    $supplier = Supplier::get();
    $tmp = Tmp_invoice_purchase::where('user_id',$user)->orderBy('updated_at','desc')->get();
    $total = new \stdClass();
    $total->quantity = $tmp->sum('quantity');
    $total->product = count($tmp);
    $total->amount = 0;
    foreach($tmp as $result){
      $total->amount += $result->total;
    }

    return view('warehouse.stock_purchase',compact('url','supplier','tmp','total','reference_no'));
  }

  public function ajaxSearchBar(Request $request)
  {
    if(!$request->barcode)
      return json_encode(false);
    $product = Warehouse_stock::where('barcode',$request->barcode)->first();
    if(!$product){
      return json_encode(false);
    }else{
      return $product;
    }
  }

  public function ajaxAddPurchaseListItem(Request $request)
  {
    $user = Auth::user()->id;

    $total = round($request->total,2);

    $result = Tmp_invoice_purchase::updateOrCreate(
                                ['barcode'=>$request->barcode,'user_id'=>$user],
                                [
                                'product_name'=>$request->product_name,
                                'measurement'=>$request->measurement,
                                'cost'=>$request->cost,
                                'quantity'=>$request->quantity,
                                'total'=>$total,
                                ]);
    if($result){
      return $result;
    }else{
      return json_encode(false);
    }

  }

  public function postStockPurchase(Request $request)
  {
    $user = Auth::user();
    $purchase_items = Tmp_invoice_purchase::where('user_id',$user->id)->get();

    if(count($purchase_items) <= 0){
      return back()->with('fail','fail'); 
    }
    
    $supplier = Supplier::where('id',$request->supplier_id)->first();
    $total_item = $purchase_items->sum('quantity');
    $total_cost = 0;

    $a = Invoice_purchase::withTrashed()
                          ->whereRaw('YEAR(created_at) = '.date("Y"))
                          ->select('id')
                          ->orderBy('id','desc')
                          ->first();
    if(!$a){
      $last_id = 1;
    }else{
      $last_id = intval($a->id) + 1;
    }
    $i=7;
    while($i>strlen($last_id)){
      $last_id = "0".$last_id;
    }
    $reference_no = "P".$last_id;

    $invoice_purchase = Invoice_purchase::create([
                                            'reference_no'=>$reference_no,
                                            'invoice_date'=>$request->invoice_date,
                                            'invoice_no'=>$request->invoice_no,
                                            'total_item'=>$total_item,
                                            'total_cost'=>0,
                                            'total_different_item'=>count($purchase_items),
                                            'supplier_id'=>$supplier->id,
                                            'supplier_name'=>$supplier->supplier_name,
                                            'creator_id'=>$user->id,
                                            'creator_name'=>$user->name,
                                            'completed'=>1,
                                          ]); 

    foreach($purchase_items as $result){
      if($result->cost != 0){
        Warehouse_stock::where('barcode',$result->barcode)
                        ->update([
                          'cost'=>$result->cost,
                          'quantity'=>DB::raw('IF (quantity IS null,0,quantity) +'.$result->quantity),
                        ]);

        Product_list::where('barcode',$result->barcode)->update(['cost'=>$result->cost]);
        Branch_product::where('barcode',$result->barcode)->update(['cost'=>$result->cost,'product_sync'=>0]);

        $total_cost += $result->total;
      }else{
        Warehouse_stock::where('barcode',$result->barcode)
                        ->update([
                          'quantity'=>DB::raw('IF (quantity IS null,0,quantity) +'.$result->quantity),
                        ]);
      }
    }

    Invoice_purchase::where('id',$invoice_purchase->id)->update(['total_cost' => $total_cost]);

    foreach($purchase_items as $result){
      $item_cost = 0;
      $item_cost = $result->total;
      Invoice_purchase_detail::create([
                                'invoice_purchase_id'=>$invoice_purchase->id,
                                'barcode'=>$result->barcode,
                                'product_name'=>$result->product_name,
                                'measurement'=>$result->measurement,
                                'cost'=>$result->cost,
                                'quantity'=>$result->quantity,
                                'total_cost'=>$item_cost,
                                'update_by'=>$user->name,
                              ]);
    }

    Tmp_invoice_purchase::where('user_id',$user->id)->delete();

    return back()->with('success','success');
  }

  public function getInvoicePurchaseHistory(Request $request)
  {
    $url = route('home')."?p=stock_menu";

    if(isset($request->filter) && $request->filter == true){
      $path = "?filter=true";
      $query = Invoice_purchase::orderBy('created_at','desc');

      if($request->ref_no != ''){
        $query->where('reference_no','LIKE','%'.$request->ref_no.'%');
        $path .= "&ref_no=".$request->ref_no;
      }else{
        $path .= "&ref_no=";
      }

      if($request->inv_no != ''){
        $query->where('invoice_no','LIKE','%'.$request->inv_no.'%');
        $path .= "&inv_no=".$request->inv_no;
      }else{
        $path .= "&inv_no=";
      }

      if($request->supplier != 'null'){
        $query->where('supplier_id',$request->supplier);
        $path .= "&supplier=".$request->supplier;
      }else{
        $path .= "&supplier=null";
      }

      if($request->date_start != ''){
        $query->where('created_at','>',$request->date_start);
        $path .= "&date_start=".$request->date_start;
      }else{
        $path .= "&date_start=";
      }

      if($request->date_end != ''){
        $query->where('created_at','<=',date("Y-m-d",strtotime($request->date_end."+1 day")));
        $path .= "&date_end=".$request->date_end;
      }else{
        $path .= "&start_end=";
      }

      $history = $query->paginate(15);
      $history->withPath($path);

    }else{

      $history = Invoice_purchase::orderby('id','desc')->paginate(15);
    }

    $supplier = Supplier::get();

    return view('warehouse.invoice_history',compact('url','history','supplier'));
  }

  public function getInvoicePurchaseHistoryDetail(Request $request)
  {
    $url = route('getInvoicePurchaseHistory');
    $invoice = Invoice_purchase::where('id',$request->invoice_id)->first();
    $invoice_detail = Invoice_purchase_detail::where('invoice_purchase_id',$request->invoice_id)->get();

    

    return view('warehouse.invoice_history_detail',compact('url','invoice','invoice_detail'));
  }

  public function ajaxDeletePurchaseListItem(Request $request)
  {
    $target = Tmp_invoice_purchase::where('id',$request->id)->first();
    Tmp_invoice_purchase::where('id',$request->id)->delete();

    return json_encode($target);
  }

  public function ajaxDeleteInvoice(Request $request)
  {
    $invoice = Invoice_purchase::where('id',$request->id)->first();
    $invoice_detail = Invoice_purchase_detail::where('invoice_purchase_id',$invoice->id)->get();

    foreach($invoice_detail as $result){
      Warehouse_stock::where('barcode',$result->barcode)
                      ->update([
                        'quantity'=>DB::raw('IF (quantity IS null,0,quantity) -'.$result->quantity),
                      ]);
    }

    Invoice_purchase::where('id',$request->id)->delete();

    return json_encode(true);
  }

  public function postInvoicePurchaseHistoryDetail(Request $request)
  {
    $user = Auth::user();
    $total = 0;
    $total_quantity = 0;
    foreach($request->invoice_purchase_detail_id as $key => $result){

      //Calculate Stock Different & Update In Warehouse Stock Table
      $qty1 = Invoice_purchase_detail::where('id',$result)->select("quantity")->first();
      $diff_qty = floatval($request->quantity[$key]) - floatval($qty1->quantity);

      if($request->cost[$key] != 0){
        Warehouse_stock::where('barcode',$request->barcode[$key])
                          ->update([
                            'cost'=>$request->cost[$key],
                            'quantity' => DB::raw('IF (quantity IS null,0,quantity) +'.$diff_qty),
                          ]);

        Branch_product::where('barcode',$request->barcode[$key])
                        ->update([
                          'cost'=>$request->cost[$key],
                          'product_sync'=>0
                        ]);
      }else{
        Warehouse_stock::where('barcode',$request->barcode[$key])
                  ->update([
                    'quantity' => DB::raw('IF (quantity IS null,0,quantity) +'.$diff_qty),
                  ]);
      }

      //Update Invoice Purchase Information
      $total_cost = floatval($request->total[$key]);
      $total += floatval($request->total[$key]);  
      $total_quantity += floatval($request->quantity[$key]);
      Invoice_purchase_detail::where('id',$result)
                              ->update([
                                'cost'=>$request->cost[$key],
                                'update_by'=>$user->name,
                                'total_cost'=>$total_cost,
                                'quantity'=>$request->quantity[$key],
                              ]);
    }
    Invoice_purchase::where('reference_no',$request->ref_no)
                      ->update([
                        'total_cost'=>$total,
                        'total_item'=>$total_quantity,
                      ]);

    return back()->with('success','success');
  }

  public function getGoodReturn()
  {
    $user = Auth::user()->id;
    $url = route('home')."?p=stock_menu";
    $a = Good_return::withTrashed()
                          ->whereRaw('YEAR(created_at) = '.date("Y"))
                          ->select('id')
                          ->orderBy('id','desc')
                          ->first();
    if(!$a){
      $last_id = 1;
    }else{
      $last_id = intval($a->id) + 1;
    }
    $i=7;
    while($i>strlen($last_id)){
      $last_id = "0".$last_id;
    }
    $gr_no = "GR".$last_id;
    $supplier = Supplier::get();
    $tmp = Tmp_good_return::where('user_id',$user)->orderBy('updated_at','desc')->get();
    $total = new \stdClass();
    $total->quantity = $tmp->sum('quantity');
    $total->product = count($tmp);
    $total->amount = 0;
    foreach($tmp as $result){
      $total->amount += $result->total;
    }

    return view('warehouse.good_return',compact('url','supplier','tmp','total','gr_no'));
  }

  public function ajaxAddGoodReturnItem(Request $request)
  {
    $user = Auth::user();

    $total = round(floatval($request->quantity) * floatval($request->cost),2);
    $result = Tmp_good_return::updateOrCreate(
                            ['user_id'=>$user->id,'barcode'=>$request->barcode],
                            [
                              'product_name'=>$request->product_name,
                              'measurement'=>$request->measurement,
                              'cost' =>$request->cost,
                              'quantity' => $request->quantity,
                              'total' => $total,
                            ]);
    return $result;
  }

  public function ajaxDeleteGoodReturnItem(Request $request)
  {
    $target = Tmp_invoice_purchase::where('id',$request->id)->first();
    Tmp_good_return::where('id',$request->id)->delete();

    return json_encode($target);
  }

  public function ajaxDeleteGrItem(Request $request)
  {
    $result = Good_return_detail::where('id',$request->id)->delete();
    $deleted = Good_return_detail::where('id',$request->id)->withTrashed()->first();

    Warehouse_stock::where('barcode',$deleted->barcode)
                    ->update([
                      'quantity' => DB::raw('IF (quantity IS null,0,quantity) +'.floatval($deleted->quantity)),
                    ]);

    return $result;
  }

  public function ajaxAddGrItem(Request $request)
  {


  }

  public function postGoodReturn(Request $request)
  {
    $user = Auth::user();
    $good_list = Tmp_good_return::where('user_id',$user->id)->get();

    if(count($good_list) <= 0){
      return back()->with('fail','fail'); 
    }

    $supplier = Supplier::where('id',$request->supplier_id)->first();
    $total_item = $good_list->sum('quantity');

    $a = Good_return::withTrashed()
                          ->whereRaw('YEAR(created_at) = '.date("Y"))
                          ->select('id')
                          ->orderBy('id','desc')
                          ->first();
    if(!$a){
      $last_id = 1;
    }else{
      $last_id = intval($a->id) + 1;
    }

    $i=7;
    while($i>strlen($last_id)){
      $last_id = "0".$last_id;
    }

    $gr_no = "GR".$last_id;

    $total_cost = 0;
    foreach($good_list as $result){
      Warehouse_stock::where('barcode',$result->barcode)
                      ->update([
                        'quantity' => DB::raw('IF (quantity IS null,0,quantity) -'.$result->quantity),
                      ]);

    $total_cost += $result->total;
    }

    $good_return = Good_return::create([
                                  'gr_no'=>$gr_no,
                                  'gr_date'=>$request->gr_date,
                                  'ref_no'=>$request->ref_no,
                                  'total_quantity'=>$total_item,
                                  'total_cost'=>$total_cost,
                                  'total_different_item'=>count($good_list),
                                  'supplier_id'=>$supplier->id,
                                  'supplier_name'=>$supplier->supplier_name,
                                  'creator_id'=>$user->id,
                                  'creator_name'=>$user->name,
                                  'completed'=>1,
                                ]); 

    foreach($good_list as $result){
      $item_cost = 0;
      $item_cost = $result->total;
      Good_return_detail::create([
                                'good_return_id'=>$good_return->id,
                                'barcode'=>$result->barcode,
                                'product_name'=>$result->product_name,
                                'measurement'=>$result->measurement,
                                'cost'=>$result->cost,
                                'quantity'=>$result->quantity,
                                'total_cost'=>$item_cost,
                                'update_by'=>$user->name,
                              ]);
    }

    Tmp_good_return::where('user_id',$user->id)->delete();

    return back()->with('success','success')->with('gr',$good_return->id);

  }

  public function getGoodReturnHistory(Request $request)
  {
    $url = route('home')."?p=stock_menu";

    if(isset($request->filter) && $request->filter == true){
      $path = "?filter=true";
      $query = Good_return::orderBy('created_at','desc');

      if($request->gr_no != ''){
        $query->where('gr_no','LIKE','%'.$request->gr_no.'%');
        $path .= "&gr_no=".$request->gr_no;
      }else{
        $path .= "&gr_no=";
      }

      if($request->ref_no != ''){
        $query->where('ref_no','LIKE','%'.$request->ref_no.'%');
        $path .= "&ref_no=".$request->ref_no;
      }else{
        $path .= "&ref_no=";
      }

      if($request->supplier != 'null'){
        $query->where('supplier_id',$request->supplier);
        $path .= "&supplier=".$request->supplier;
      }else{
        $path .= "&supplier=null";
      }

      if($request->date_start != ''){
        $query->where('created_at','>',$request->date_start);
        $path .= "&date_start=".$request->date_start;
      }else{
        $path .= "&date_start=";
      }

      if($request->date_end != ''){
        $query->where('created_at','<=',date("Y-m-d",strtotime($request->date_end."+1 day")));
        $path .= "&date_end=".$request->date_end;
      }else{
        $path .= "&start_end=";
      }

      $gr_list = $query->paginate(15);
      $gr_list->withPath($path);

    }else{
      $gr_list = Good_return::orderby('id','desc')->paginate(15);
    }

    $supplier = Supplier::all();

    return view('warehouse.good_return_history',compact('gr_list','url','supplier'));
  }

  public function getGoodReturnHistoryDetail(Request $request)
  {
    $url = url()->previous();

    $gr = Good_return::where('id',$request->id)->first();

    if($gr == null){
      return redirect($url);
    }

    $gr_detail = Good_return_detail::where('good_return_id',$gr->id)->get();
    $supplier = Supplier::all();

    return view('warehouse.good_return_history_detail',compact('url','gr_detail','gr','supplier'));
  }

  public function ajaxChangeSupplier(Request $request)
  {
    $supplier = Supplier::where('id',$request->id)->first();

    $status = Good_return::where('gr_no',$request->gr_no)
                          ->update([
                            'supplier_id' => $supplier->id,
                            'supplier_name' => $supplier->supplier_name
                          ]);
    return $status;
  }

  public function ajaxDeleteGr(Request $request)
  {
    $gr = Good_return::where('gr_no',$request->gr_id)->first();
    $gr_detail = Good_return_detail::where('good_return_id',$gr->id)->get();

    foreach($gr_detail as $result){
      Warehouse_stock::where('barcode',$result->barcode)
                      ->update([
                        'quantity'=>DB::raw('IF (quantity IS null,0,quantity) +'.$result->quantity),
                      ]);
    }

    Good_return::where('gr_no',$request->gr_id)->delete();

    return json_encode(true);
  }

  public function postGoodReturnHistoryDetail(Request $request)
  {
    $user = Auth::user();
    $total = 0;
    $total_quantity = 0;
    foreach($request->gr_detail_id as $key => $result){

      //Calculate Stock Different & Update In Warehouse Stock Table
      $qty1 = Good_return_detail::where('id',$result)->select("quantity")->first();
      $diff_qty = floatval($request->quantity[$key]) - floatval($qty1->quantity);

      Warehouse_stock::where('barcode',$request->barcode[$key])
                      ->update([
                        'quantity' => DB::raw('IF (quantity IS null,0,quantity) -'.$diff_qty),
                      ]);
      
      //Update Invoice Purchase Information
      $total_cost = floatval($request->total[$key]);
      $total += floatval($request->total[$key]);  
      $total_quantity += floatval($request->quantity[$key]);
      Good_return_detail::where('id',$result)
                              ->update([
                                'update_by'=>$user->name,
                                'cost'=>$request->cost[$key],
                                'total_cost'=>$total_cost,
                                'quantity'=>$request->quantity[$key],
                              ]);
    }
    
    Good_return::where('gr_no',$request->gr_no)
                      ->update([
                        'total_cost'=>$total,
                        'total_quantity'=>$total_quantity,
                      ]);

    return back()->with('success','success');
  }

  public function getPrintGr(Request $request)
  {
    $gr = Good_return::where('id',$request->id)->first();
    $supplier = Supplier::where('id',$gr->supplier_id)->first();
    $gr_detail = Good_return_detail::where('good_return_id',$gr->id)->get();

    return view('print.print_gr',compact('supplier','gr','gr_detail'));
  }

  public function getStockWriteOff()
  { 
    $url = route('home')."?p=stock_menu";
    $target = new \stdClass();

    if(isset($_GET['search']) && $_GET['search'] != ""){
      $target = Warehouse_stock::where('barcode',$_GET['search'])->first();
      $warehouse_stock = Warehouse_stock::where('barcode','LIKE','%'.$_GET['search'].'%')
                                          ->where('barcode','!=',$_GET['search'])
                                          ->orWhere(function($query){
                                            $query->where('barcode','!=',$_GET['search'])
                                                  ->where('product_name','LIKE','%'.$_GET['search'].'%');
                                          })
                                          ->orderBy('updated_at','desc')
                                          ->paginate(20);
    }else{
      $warehouse_stock = Warehouse_stock::orderBy('updated_at','desc')
                                        ->paginate(20);
    }
    $page = isset($_GET['page']) ? $_GET['page'] : null;

    return view('warehouse.write_off',compact('url','warehouse_stock','target','page'));
  }

  public function ajaxAddWriteOffItem(Request $request)
  {
    $item = Warehouse_stock::where('id',$request->warehouse_stock_id)->first();
    $user = Auth::user();

    $total = $request->order_quantity * $item->cost;

    Write_off_detail::updateOrCreate(
                    ['barcode' => $item->barcode,'completed'=>0],
                    [
                      'product_name'=>$item->product_name,
                      'quantity'=>$request->order_quantity,
                      'cost'=>$item->cost,
                      'total'=>$total,
                      'created_by'=>$user->name,
                    ]);

    return json_encode(true);
  }

  public function getStockWriteOffList()
  {
    $url = route('getStockWriteOff');

    $wf_list = Write_off_detail::where('completed',0)->get();

    return view('warehouse.write_off_list',compact('url','wf_list'));
  }

  public function ajaxRemoveWriteOffItem(Request $request)
  {
    Write_off_detail::where('id',$request->id)->delete();

    return json_encode(true);
  }

  public function postWriteOffList(Request $request)
  {
    $user = Auth::user();
    $seq = Str::random(4);
    $seq = date('Ymd',strtotime(now())).'-'.$seq;
    if(Write_off::where('seq_no',$seq)->count() == 0){

      $wfd = Write_off_detail::where('completed',0)
                              ->where('seq_no',null)
                              ->where('write_off_id',null)
                              ->get();

      foreach($wfd as $key => $result){
        Warehouse_stock::where('barcode',$result->barcode)
                        ->update([
                          'quantity' => DB::raw('IF (quantity IS null,0,quantity) -'.$result->quantity),
                        ]);
      }

      $wf = Write_off::create([
              'seq_no'=>$seq,
              'total_item'=>$request->total_item,
              'total_amount'=>$request->total_cost,
              'created_by'=>$user->name,
              'write_off_date'=>now(),
              'completed'=>1,
            ]);

      Write_off_detail::where('completed',0)
                        ->where('seq_no',null)
                        ->where('write_off_id',null)
                        ->update([
                          'write_off_id'=>$wf->id,
                          'seq_no'=>$seq,
                          'completed'=>1,
                        ]);
      return json_encode(true);
    }else{
      return json_encode(false);
    }
  }

  public function getWriteOffHistory()
  {
    $url = route('home')."?p=stock_menu";
    $wf = Write_off::orderBy('created_at','desc')->paginate(15);

    return view('warehouse.write_off_history',compact('url','wf'));
  }

  public function ajaxDeleteWriteOffRecord(Request $request)
  {
    $wfd = Write_off_detail::where('write_off_id',$request->id)->get();

    foreach($wfd as $key => $result){
      Warehouse_stock::where('barcode',$result->barcode)
                        ->update([
                          'quantity' => DB::raw('IF (quantity IS null,0,quantity) +'.$result->quantity),
                        ]);
    }

    Write_off::where('id',$request->id)->delete();

    return json_encode(true);
  }

  public function getWriteOffPrint(Request $request)
  { 
    // PDF Example
    // $pdf = Facade::loadView('print.print_write_off');
    // return $pdf->download('test.pdf');

    $wf = Write_off::where('id',$request->id)->first();
    $wf_list = Write_off_detail::where('write_off_id',$request->id)->get();

    return view('print.print_write_off',compact('wf','wf_list'));
  }

}
