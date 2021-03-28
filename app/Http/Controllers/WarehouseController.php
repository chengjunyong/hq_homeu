<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Warehouse_stock;
use App\Department;
use App\Category;
use App\Supplier;

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
                              ->where('warehouse_stock.reorder_level','>=','warehouse_stock.quantity')
                              ->select('department.department_name','category.category_name','warehouse_stock.*')
                              ->get();

    return view('purchase_order',compact('url','supplier','items'));
  }

  public function ajaxGetSupplier(Request $request)
  {

    dd("abc");
    
  }

}
