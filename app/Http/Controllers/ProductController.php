<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Branch_product;
use App\Department;
use App\Category;
use App\Product_list;
use App\Product_configure;
use App\Branch;


class ProductController extends Controller
{
  public function getProductList()
  { 
    $access = app('App\Http\Controllers\UserController')->checkAccessControl();
    if(!$access)
    {
      return view('not_access');
    }

    $search = null;
  	$product_list = Product_list::join('department','department.id','=','product_list.department_id')
                                ->join('category','category.id','=','product_list.category_id')
                                ->select('product_list.*','department.department_name','category.category_name')
                                ->paginate(14);

  	return view('product_list',compact('product_list','search'));
  }

  public function searchProduct(Request $request)
  {
    $search = $request->search;

  	$product_list = Product_list::join('department','department.id','=','product_list.department_id')
                            ->join('category','category.id','=','product_list.category_id')
                            ->select('product_list.*','department.department_name','category.category_name')
                            ->where('product_list.barcode','LIKE','%'.$request->search.'%')
  													->orWhere('product_list.product_name','LIKE','%'.$request->search.'%')
  													->paginate(14);

  	$product_list->withPath('?search='.$request->search);

  	return view('product_list',compact('product_list','search'));
  }

  public function ajaxAddProduct(Request $request)
  {
    Product::create([
			'branch_id'=>'1',
			'barcode'=>$request->barcode,
			'product_name'=>$request->product_name,
			'price'=>$request->price,
			'quantity'=>$request->quantity,
    ]);

    return 'true';
  }

  public function getProductConfig()
  {
    $access = app('App\Http\Controllers\UserController')->checkAccessControl();
    if(!$access)
    {
      return view('not_access');
    }
    
    $result = Product_configure::first();

    if($result == null){
      $result = new \stdClass();
      $result->default_price_margin = 0;
      $result->below_sales_price = true;
    }

    return view('productconfig',compact('result'));
  }

  public function postProductConfig(Request $request)
  {
    $result = Product_configure::updateOrCreate(['id' => '1'],['default_price_margin' => $request->percentage,'below_sales_price' => $request->lower_than_cost,]);

    if($result){
      return "true";
    }else{
      return "false";
    }
  }

  public function getAddProduct()
  {
    $access = app('App\Http\Controllers\UserController')->checkAccessControl();
    if(!$access)
    {
      return view('not_access');
    }

    $department = Department::orderBy('id','asc')->get();

    $category = Category::where('department_id',$department->first()->id)->get();

    $default_price = Product_configure::first();

    return view('addproduct',compact('department','category','default_price'));
  }

  public function ajaxGetCategory(Request $request)
  {

    $category = Category::where('department_id',$request->department_id)
                          ->get()
                          ->toArray();

    return $category;

  }

  public function postAddProduct(Request $request)
  {
    $branch = Branch::select('id')->get();

    foreach($branch as $result){
      Branch_product::create([
        'branch_id'=>$result->id,
        'department_id'=>$request->department,
        'category_id'=>$request->category,
        'barcode'=>$request->barcode,
        'product_name'=>$request->product_name,
        'cost'=>$request->cost,
        'price'=>$request->price,
        'quantity'=>0,
        'reorder_level'=>$request->reorder_level,
        'recommend_quantity'=>$request->recommend_quantity,
        'unit_type'=>null,
        'product_sync'=>0,
      ]);
    }

    Product_list::create([
      'department_id'=>$request->department,
      'category_id'=>$request->category,
      'barcode'=>$request->barcode,
      'product_name'=>$request->product_name,
      'cost'=>$request->cost,
      'price'=>$request->price,
      'quantity'=>0,
      'reorder_level'=>$request->reorder_level,
      'recommend_quantity'=>$request->recommend_quantity,
      'unit_type'=>null,
    ]);

    return back()->with('result','true');
  }

  public function ajaxGetBarcode(Request $request)
  {
    $result = Product_list::where('barcode',$request->barcode)->exists();

    if($result){
      return "true";
    }else{
      return "false";
    }
  }


  public function getModifyProduct(Request $request)
  {
    $product = Product_list::where('id',$request->id)->first();

    $department = Department::orderBy('id','asc')->get();

    $category = Category::where('department_id',$product->department_id)->get();

    $default_price = Product_configure::first();

    return view('modifyproduct',compact('product','department','category','default_price'));
  }

  public function postModifyProduct(Request $request)
  {

    $branch = Branch::select('id')->get();

    Branch_product::where('barcode',$request->barcode)
                    ->update([
                      'department_id'=>$request->department,
                      'category_id'=>$request->category,
                      'product_name'=>$request->product_name,
                      'cost'=>$request->cost,
                      'price'=>$request->price,
                      'reorder_level'=>$request->reorder_level,
                      'recommend_quantity'=>$request->recommend_quantity,
                      'product_sync'=>0,
                    ]);

    Product_list::where('barcode',$request->barcode)
                    ->update([
                      'department_id'=>$request->department,
                      'category_id'=>$request->category,
                      'product_name'=>$request->product_name,
                      'cost'=>$request->cost,
                      'price'=>$request->price,
                      'reorder_level'=>$request->reorder_level,
                      'recommend_quantity'=>$request->recommend_quantity,
                    ]);

    return back()->with('result','true');
  }


}
