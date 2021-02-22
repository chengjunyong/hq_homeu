<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Branch;
use App\Branch_product;
use App\Product_list;
use App\Product_configure;
use App\Do_detail;
use App\Do_configure;
use App\Do_list;


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

}
