<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Warehouse_stock;
use App\Department;
use App\Category;
use App\Supplier;
use App\Purchase_order;
use App\Purchase_order_detail;

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
  										'department_id' => $request->department,
  										'category_id' => $request->category,
  										'product_name' => $request->product_name,
  										'cost' => $request->cost,
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
    $total_cost = Warehouse_stock::whereIn('id',$request->product_id)->sum('cost');

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
    $po_detail = Purchase_order_detail::where('po_id',$request->id)->get();
    $total = 0;
    foreach($po_detail as $result){
      $total += floatval($result->cost) * intval($result->quantity);
    }

    return view('print_po',compact('po','po_detail','total'));
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

}
