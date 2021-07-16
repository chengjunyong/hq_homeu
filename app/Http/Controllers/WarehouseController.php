<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
  		$warehouse_stock = Warehouse_stock::where('product_name','LIKE','%'.$_GET['search'].'%')
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
      $total += floatval($result->cost) * intval($result->quantity);
    }

    return view('print_po',compact('po','po_detail','total','supplier'));
  }

  public function getPurchaseOrderHistory()
  {
    $url = route('home')."?p=stock_menu";

    $po = Purchase_order::join('supplier','supplier.id','=','purchase_order.supplier_id')
                          ->select('purchase_order.*','supplier.supplier_name')
                          ->orderBy('created_at','desc')
                          ->paginate(10);

    return view('purchase_order_history',compact('url','po'));
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
        'remark' => $request->remark[$key],
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
                        'quantity' => DB::raw('quantity +'.$request->received_quantity[$key]),
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

    $warehouse_stock = Warehouse_stock::get();
    $supplier = Supplier::get();


    return view('warehouse.manual_issue_purchase_order',compact('url','warehouse_stock','supplier'));
  }

  public function ajaxAddManualStock(Request $request)
  { 
    $warehouse_stock = Warehouse_stock::where('id',$request->warehouse_stock_id)->first();
    $supplier = Supplier::where('id',$request->supplier_id)->first();

    try{
      $result = Tmp_purchase_list::updateOrCreate(
                              ['supplier_id'=>$request->supplier_id,'warehouse_stock_id' => $warehouse_stock->id,]
                              ,[
                                'supplier_name' => $supplier->supplier_name,
                                'department_id' => $warehouse_stock->department_id,
                                'category_id' => $warehouse_stock->category_id,
                                'barcode' => $warehouse_stock->barcode,
                                'product_name' => $warehouse_stock->product_name,
                                'cost' => $warehouse_stock->cost,
                                'price' => $warehouse_stock->price,
                                'order_quantity' => $request->order_quantity,
                              ]);
      return "true";

    }catch(Throwable $e){
      return $e;
    }

  }

  public function getPurchaseOrderList()
  {
    $url = route('getManualIssuePurchaseOrder');
    $warehouse_group = Tmp_purchase_list::select(DB::raw('DISTINCT supplier_id'))->first();
    if($warehouse_group == null){ 
      return "<script>
              alert('No order data, you will be redirect to order page shortly');
              window.location.assign('".route('getManualIssuePurchaseOrder')."');
              </script>";
    }
    $supplier = Supplier::where('id',$warehouse_group->supplier_id)->first();
    $tmp = Tmp_purchase_list::where('supplier_id',$warehouse_group->supplier_id)->get();

    $total_item = 0;
    $total_cost = 0;
    foreach($tmp as $result){
      $total_item += $result->order_quantity; 
      $total_cost += intval($result->order_quantity) * doubleval($result->cost);
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

  public function postManualPurchaseOrderList(Request $request)
  {
    $warehouse_stock = Warehouse_stock::whereIn('id',$request->product_id)->get();
    $supplier = Supplier::where('id',$request->supplier_id)->first();
    $date = strtotime(date("Y-m-d h:i:s"));
    $po_number = "PO".$date;

    $purchase_order = Purchase_order::create([
                        'po_number' => $po_number,
                        'supplier_id' => $supplier->id,
                        'supplier_code' => $supplier->supplier_code,
                        'supplier_name' => $supplier->supplier_name,
                        'total_quantity_items' => array_sum($request->order_quantity),
                        'total_amount' => $request->total_cost,
                        'issue_date' => $request->issue_date,
                      ]);

    foreach($warehouse_stock as $key => $result){
      Purchase_order_detail::create([
        'po_id' => $purchase_order->id,
        'po_number' => $po_number,
        'product_id' => $result->id,
        'barcode' => $result->barcode,
        'product_name' => $result->product_name,
        'cost' => $result->cost,
        'quantity' => $request->order_quantity[$key],
        'received' => 0,
      ]);
    }

    //Delete item from Tmp_order_list
    Tmp_purchase_list::where('supplier_id',$request->supplier_id)
                    ->delete();

    return json_encode(true);
  }

  public function getStockPurchase()
  {
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
    $i=5;
    while($i>strlen($last_id)){
      $last_id = "0".$last_id;
    }
    $reference_no = "HOMEU".date("Y").$last_id;
    $supplier = Supplier::get();
    $tmp = Tmp_invoice_purchase::orderBy('updated_at','desc')->get();
    $total = new \stdClass();
    $total->quantity = $tmp->sum('quantity');
    $total->product = count($tmp);
    $total->amount = 0;
    foreach($tmp as $result){
      $total->amount += ($result->quantity * $result->cost);
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
    $result = Tmp_invoice_purchase::updateOrCreate(
                                ['barcode'=>$request->barcode],
                                [
                                'product_name'=>$request->product_name,
                                'cost'=>$request->cost,
                                'quantity'=>$request->quantity,
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
    $purchase_items = Tmp_invoice_purchase::get();
    $supplier = Supplier::where('id',$request->supplier_id)->first();
    $total_item = $purchase_items->sum('quantity');
    $total_cost = 0;

    foreach($purchase_items as $result){
      Warehouse_stock::where('barcode',$result->barcode)
                      ->update([
                        'cost'=>$result->cost,
                        'quantity'=>DB::raw('IF (quantity IS null,0,quantity) +'.$result->quantity),
                      ]);

      Product_list::where('barcode',$result->barcode)->update(['cost'=>$result->cost]);
      Branch_product::where('barcode',$result->barcode)->update(['cost'=>$result->cost]);

      $total_cost += ($result->cost * $result->quantity);
    }

    $invoice_purchase = Invoice_purchase::create([
                                            'reference_no'=>$request->reference_no,
                                            'invoice_date'=>$request->invoice_date,
                                            'invoice_no'=>$request->invoice_no,
                                            'total_item'=>$total_item,
                                            'total_cost'=>$total_cost,
                                            'total_different_item'=>count($purchase_items),
                                            'supplier_id'=>$supplier->id,
                                            'supplier_name'=>$supplier->supplier_name,
                                            'creator_id'=>$user->id,
                                            'creator_name'=>$user->name,
                                            'completed'=>1,
                                          ]);

    foreach($purchase_items as $result){
      Invoice_purchase_detail::create([
                                'invoice_purchase_id'=>$invoice_purchase->id,
                                'barcode'=>$result->barcode,
                                'product_name'=>$result->product_name,
                                'cost'=>$result->cost,
                                'quantity'=>$result->quantity,
                              ]);
    }

    Tmp_invoice_purchase::truncate();

    return back()->with('success','success');
  }

  public function getInvoicePurchaseHistory()
  {
    $url = route('home')."?p=stock_menu";

    $history = Invoice_purchase::orderby('created_at','desc')->paginate(15);

    return view('warehouse.invoice_history',compact('url','history'));
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
    $invoice = Invoice_purchase::where('reference_no',$request->ref_id)->first();
    $invoice_detail = Invoice_purchase_detail::where('invoice_purchase_id',$invoice->id)->get();

    foreach($invoice_detail as $result){
      Warehouse_stock::where('barcode',$result->barcode)
                      ->update([
                        'quantity'=>DB::raw('IF (quantity IS null,0,quantity) -'.$result->quantity),
                      ]);
    }

    Invoice_purchase::where('reference_no',$request->ref_id)->delete();

    return json_encode(true);
  }

}
