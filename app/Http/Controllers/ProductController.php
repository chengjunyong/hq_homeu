<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Branch_product;
use App\Department;
use App\Category;
use App\Product_list;


class ProductController extends Controller
{
    public function getProductList()
    {
    	$product_list = Product_list::join('department','department.id','=','product_list.department_id')
                                  ->join('category','category.id','=','product_list.category_id')
                                  ->select('product_list.*','department.department_name','category.category_name')
                                  ->paginate(25);

    	return view('product_list',compact('product_list'));
    }

    public function searchProduct(Request $request)
    {
    	$product_list = Branch_product::join('department','department.id','=','product_list.department_id')
                              ->join('category','category.id','=','product_list.category_id')
                              ->select('product_list.*','department.department_name','category.category_name')
                              ->where('product_list.barcode','LIKE','%'.$request->search.'%')
    													->orWhere('product_list.product_name','LIKE','%'.$request->search.'%')
    													->paginate(25);

    	$product_list->withPath('?search='.$request->search);

    	return view('product_list',compact('product_list'));
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
}
