<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Branch;
use App\Branch_product;
use App\Product_list;
use App\Product_configure;


class BranchController extends Controller
{
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


    dd($request);

  }

}
