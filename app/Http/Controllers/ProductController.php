<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Branch_product;
use App\Department;
use App\Category;
use App\Product_list;
use App\Product_configure;
use App\Branch;
use App\Warehouse_stock;
use App\Voucher;
use App\Supplier;
use App\Product_supplier;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth', 'user_access']);
  }
    
  public function getProductList()
  { 
    $access = app('App\Http\Controllers\UserController')->checkAccessControl();
    if(!$access)
    {
      return view('not_access');
    }

    $url = route('home')."?p=product_menu";

    $search = null;
  	$product_list = Product_list::join('department','department.id','=','product_list.department_id')
                                ->join('category','category.id','=','product_list.category_id')
                                ->select('product_list.*','department.department_name','category.category_name')
                                ->paginate(14);

  	return view('product_list',compact('product_list','search','url'));
  }

  public function searchProduct(Request $request)
  {
    $url = route('home')."?p=product_menu";

    $search = $request->search;

  	$product_list = Product_list::join('department','department.id','=','product_list.department_id')
                            ->join('category','category.id','=','product_list.category_id')
                            ->select('product_list.*','department.department_name','category.category_name')
                            ->where('product_list.barcode','LIKE','%'.$request->search.'%')
  													->orWhere('product_list.product_name','LIKE','%'.$request->search.'%')
  													->paginate(14);

  	$product_list->withPath('?search='.$request->search);

  	return view('product_list',compact('product_list','search','url'));
  }

  // public function ajaxAddProduct(Request $request)
  // {
  //   Product::updateOrCreate(
  //     [
		// 	'branch_id'=>'1',
		// 	'barcode'=>$request->barcode,
		// 	'product_name'=>$request->product_name,
		// 	'price'=>$request->price,
		// 	'quantity'=>$request->quantity,
  //   ]);

  //   return 'true';
  // }

  public function getProductConfig()
  {
    $access = app('App\Http\Controllers\UserController')->checkAccessControl();
    if(!$access)
    {
      return view('not_access');
    }
    
    $url = route('home')."?p=product_menu";

    $result = Product_configure::first();

    if($result == null){
      $result = new \stdClass();
      $result->default_price_margin = 0;
      $result->below_sales_price = true;
    }

    return view('productconfig',compact('result','url'));
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

    $url = route('home')."?p=product_menu";

    $department = Department::orderBy('id','asc')->get();

    $category = Category::where('department_id',$department->first()->id)->get();

    $default_price = Product_configure::first();

    return view('addproduct',compact('department','category','default_price','url'));
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
      Branch_product::updateOrCreate(
        ['barcode'=>$request->barcode],
        [
        'branch_id'=>$result->id,
        'department_id'=>$request->department,
        'category_id'=>$request->category,
        'product_name'=>$request->product_name,
        'uom'=>$request->uom,
        'measurement'=>$request->measurement,
        'cost'=>$request->cost,
        'price'=>$request->price,
        'quantity'=>0,
        'reorder_level'=>$request->reorder_level,
        'recommend_quantity'=>$request->recommend_quantity,
        'unit_type'=>null,
        'product_sync'=>0,
      ]);
    }

    Product_list::updateOrCreate(
      ['barcode'=>$request->barcode],
      [
      'department_id'=>$request->department,
      'category_id'=>$request->category,
      'product_name'=>$request->product_name,
      'uom'=>$request->uom,
      'measurement'=>$request->measurement,
      'cost'=>$request->cost,
      'price'=>$request->price,
      'quantity'=>0,
      'reorder_level'=>$request->reorder_level,
      'recommend_quantity'=>$request->recommend_quantity,
      'unit_type'=>null,
    ]);

    Warehouse_stock::updateOrCreate(
      ['barcode'=>$request->barcode],
      [
      'department_id'=>$request->department,
      'category_id'=>$request->category,
      'product_name'=>$request->product_name,
      'uom'=>$request->uom,
      'measurement'=>$request->measurement,
      'cost'=>$request->cost,
      'price'=>$request->price,
      'quantity'=>0,
      'reorder_level'=>$request->reorder_level,
      'reorder_quantity'=>$request->recommend_quantity,
      'unit_type'=>null,
      'product_sync'=>null,
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
    $url = route('getProductList');

    $product = Product_list::where('id',$request->id)->first();

    $department = Department::orderBy('id','asc')->get();

    $category = Category::where('department_id',$product->department_id)->get();

    $default_price = Product_configure::first();

    $supplier_list = Product_supplier::join('supplier','product_supplier.supplier_id','=','supplier.id')
                                      ->where('product_supplier.product_id',$product->id)
                                      ->select('supplier.supplier_name','supplier.supplier_code','supplier.contact_number','product_supplier.*','supplier.id as s_id')
                                      ->get();

    $existing_supplier = $supplier_list->toArray();

    $supplier = Supplier::whereNotIn('id',array_column($existing_supplier,'s_id'))
                          ->get();

    return view('modifyproduct',compact('product','department','category','default_price','url','supplier','supplier_list'));
  }

  public function ajaxAddSupplier(Request $request)
  {
    Product_supplier::updateOrCreate([
        'product_id' => $request->product_id,
        'supplier_id' => $request->supplier_id,
    ]);

    $result = Supplier::where('id',$request->supplier_id)->first();

    return $result;
  }

  public function ajaxDeleteSupplier(Request $request)
  {
    Product_supplier::where('id',$request->id)->delete();

    return json_encode(true);
  }

  public function ajaxTriggerProductSync(Request $request)
  {
    $product = Product_list::where('barcode',$request->barcode)->first();

    Branch_product::where('barcode',$request->barcode)
                  ->update([
                    'product_sync'=>0,
                    'measurement'=>$product->measurement,
                    'cost'=>$product->cost,
                    'price'=>$product->price,
                    'normal_wholesale_price'=>$product->normal_wholesale_price,
                    'normal_wholesale_price2'=>$product->normal_wholesale_price2,
                    'normal_wholesale_price3'=>$product->normal_wholesale_price3,
                    'normal_wholesale_price4'=>$product->normal_wholesale_price4,
                    'normal_wholesale_price5'=>$product->normal_wholesale_price5,
                    'normal_wholesale_price6'=>$product->normal_wholesale_price6,
                    'normal_wholesale_price7'=>$product->normal_wholesale_price7,
                    'normal_wholesale_quantity'=>$product->normal_wholesale_quantity,
                    'normal_wholesale_quantity2'=>$product->normal_wholesale_quantity2,
                    'normal_wholesale_quantity3'=>$product->normal_wholesale_quantity3,
                    'normal_wholesale_quantity4'=>$product->normal_wholesale_quantity4,
                    'normal_wholesale_quantity5'=>$product->normal_wholesale_quantity5,
                    'normal_wholesale_quantity6'=>$product->normal_wholesale_quantity6,
                    'normal_wholesale_quantity7'=>$product->normal_wholesale_quantity7,
                    'wholesale_price'=>$product->wholesale_price,
                    'wholesale_price2'=>$product->wholesale_price2,
                    'wholesale_price3'=>$product->wholesale_price3,
                    'wholesale_price4'=>$product->wholesale_price4,
                    'wholesale_price5'=>$product->wholesale_price5,
                    'wholesale_price6'=>$product->wholesale_price6,
                    'wholesale_price7'=>$product->wholesale_price7,
                    'wholesale_quantity'=>$product->wholesale_quantity,
                    'wholesale_quantity2'=>$product->wholesale_quantity2,
                    'wholesale_quantity3'=>$product->wholesale_quantity3,
                    'wholesale_quantity4'=>$product->wholesale_quantity4,
                    'wholesale_quantity5'=>$product->wholesale_quantity5,
                    'wholesale_quantity6'=>$product->wholesale_quantity6,
                    'wholesale_quantity7'=>$product->wholesale_quantity7,
                    'wholesale_start_date'=>$product->wholesale_start_date,
                    'wholesale_end_date'=>$product->wholesale_end_date,
                    'promotion_price'=>$product->promotion_price,
                    'promotion_start'=>$product->promotion_start,
                    'promotion_end'=>$product->promotion_end,
                  ]);

    return json_encode(true);
  }

  public function postModifyProduct(Request $request)
  {
    Branch_product::where('barcode',$request->barcode)
                    ->update([
                      'department_id'=>$request->department,
                      'category_id'=>$request->category,
                      'product_name'=>$request->product_name,
                      'uom'=>$request->uom,
                      'measurement'=>$request->measurement,
                      'cost'=>$request->cost,
                      'price'=>$request->price,
                      'normal_wholesale_price'=>$request->normal_wholesales_price,
                      'normal_wholesale_quantity'=>$request->normal_wholesales_quantity,
                      'normal_wholesale_price2'=>$request->normal_wholesales_price2,
                      'normal_wholesale_quantity2'=>$request->normal_wholesales_quantity2,
                      'normal_wholesale_price3'=>$request->normal_wholesales_price3,
                      'normal_wholesale_quantity3'=>$request->normal_wholesales_quantity3,
                      'normal_wholesale_price4'=>$request->normal_wholesales_price4,
                      'normal_wholesale_quantity4'=>$request->normal_wholesales_quantity4,
                      'normal_wholesale_price5'=>$request->normal_wholesales_price5,
                      'normal_wholesale_quantity5'=>$request->normal_wholesales_quantity5,
                      'normal_wholesale_price6'=>$request->normal_wholesales_price6,
                      'normal_wholesale_quantity6'=>$request->normal_wholesales_quantity6,
                      'normal_wholesale_price7'=>$request->normal_wholesales_price7,
                      'normal_wholesale_quantity7'=>$request->normal_wholesales_quantity7,
                      'reorder_level'=>$request->reorder_level,
                      'recommend_quantity'=>$request->recommend_quantity,
                      'promotion_start'=>$request->promotion_start,
                      'promotion_end'=>$request->promotion_end,
                      'promotion_price'=>$request->promotion_price,
                      'wholesale_price'=>$request->wholesales_price,
                      'wholesale_quantity'=>$request->wholesales_quantity,
                      'wholesale_price2'=>$request->wholesales_price2,
                      'wholesale_quantity2'=>$request->wholesales_quantity2,
                      'wholesale_price'=>$request->wholesales_price,
                      'wholesale_quantity'=>$request->wholesales_quantity,
                      'wholesale_price2'=>$request->wholesales_price2,
                      'wholesale_quantity2'=>$request->wholesales_quantity2,
                      'wholesale_price3'=>$request->wholesales_price3,
                      'wholesale_quantity3'=>$request->wholesales_quantity3,
                      'wholesale_price4'=>$request->wholesales_price4,
                      'wholesale_quantity4'=>$request->wholesales_quantity4,
                      'wholesale_price5'=>$request->wholesales_price5,
                      'wholesale_quantity5'=>$request->wholesales_quantity5,
                      'wholesale_price6'=>$request->wholesales_price6,
                      'wholesale_quantity6'=>$request->wholesales_quantity6,
                      'wholesale_price7'=>$request->wholesales_price7,
                      'wholesale_quantity7'=>$request->wholesales_quantity7,
                      'wholesale_start_date'=>$request->wholesales_start,
                      'wholesale_end_date'=>$request->wholesales_end,
                      'product_sync'=>0,
                    ]);

    Product_list::where('barcode',$request->barcode)
                    ->update([
                      'department_id'=>$request->department,
                      'category_id'=>$request->category,
                      'product_name'=>$request->product_name,
                      'uom'=>$request->uom,
                      'measurement'=>$request->measurement,
                      'cost'=>$request->cost,
                      'price'=>$request->price,
                      'normal_wholesale_price'=>$request->normal_wholesales_price,
                      'normal_wholesale_quantity'=>$request->normal_wholesales_quantity,
                      'normal_wholesale_price2'=>$request->normal_wholesales_price2,
                      'normal_wholesale_quantity2'=>$request->normal_wholesales_quantity2,
                      'normal_wholesale_price3'=>$request->normal_wholesales_price3,
                      'normal_wholesale_quantity3'=>$request->normal_wholesales_quantity3,
                      'normal_wholesale_price4'=>$request->normal_wholesales_price4,
                      'normal_wholesale_quantity4'=>$request->normal_wholesales_quantity4,
                      'normal_wholesale_price5'=>$request->normal_wholesales_price5,
                      'normal_wholesale_quantity5'=>$request->normal_wholesales_quantity5,
                      'normal_wholesale_price6'=>$request->normal_wholesales_price6,
                      'normal_wholesale_quantity6'=>$request->normal_wholesales_quantity6,
                      'normal_wholesale_price7'=>$request->normal_wholesales_price7,
                      'normal_wholesale_quantity7'=>$request->normal_wholesales_quantity7,
                      'reorder_level'=>$request->reorder_level,
                      'recommend_quantity'=>$request->recommend_quantity,
                      'schedule_date'=>$request->schedule_date,
                      'schedule_price'=>$request->schedule_price,
                      'promotion_start'=>$request->promotion_start,
                      'promotion_end'=>$request->promotion_end,
                      'promotion_price'=>$request->promotion_price,
                      'wholesale_price'=>$request->wholesales_price,
                      'wholesale_quantity'=>$request->wholesales_quantity,
                      'wholesale_price2'=>$request->wholesales_price2,
                      'wholesale_quantity2'=>$request->wholesales_quantity2,
                      'wholesale_price'=>$request->wholesales_price,
                      'wholesale_quantity'=>$request->wholesales_quantity,
                      'wholesale_price2'=>$request->wholesales_price2,
                      'wholesale_quantity2'=>$request->wholesales_quantity2,
                      'wholesale_price3'=>$request->wholesales_price3,
                      'wholesale_quantity3'=>$request->wholesales_quantity3,
                      'wholesale_price4'=>$request->wholesales_price4,
                      'wholesale_quantity4'=>$request->wholesales_quantity4,
                      'wholesale_price5'=>$request->wholesales_price5,
                      'wholesale_quantity5'=>$request->wholesales_quantity5,
                      'wholesale_price6'=>$request->wholesales_price6,
                      'wholesale_quantity6'=>$request->wholesales_quantity6,
                      'wholesale_price7'=>$request->wholesales_price7,
                      'wholesale_quantity7'=>$request->wholesales_quantity7,
                      'wholesale_start_date'=>$request->wholesales_start,
                      'wholesale_end_date'=>$request->wholesales_end,
                      'product_sync'=>0,
                    ]);

    Warehouse_stock::where('barcode',$request->barcode)
                    ->update([
                      'department_id'=>$request->department,
                      'category_id'=>$request->category,
                      'product_name'=>$request->product_name,
                      'uom'=>$request->uom,
                      'measurement'=>$request->measurement,
                      'cost'=>$request->cost,
                      'price'=>$request->price,
                      'reorder_level'=>0,
                      'reorder_quantity'=>0,
                      'product_sync'=>null,
                    ]);                

    $user = Auth::user();
    $log = "User -> $user->name, ID -> $user->id\n";
    $log .= json_encode($request->toArray());
    Log::channel('productModify')->info($log);

    return back()->with('result','true');
  }

  public function getVoucher()
  {
    $access = app('App\Http\Controllers\UserController')->checkAccessControl();
    if(!$access)
    {
      return view('not_access');
    }
    $url = route('home')."?p=product_menu";

    $voucher = Voucher::paginate(20);

    return view('voucher',compact('url','voucher'));
  }

  public function postVoucher(Request $request)
  {
    $user = Auth::user();

    if($request->type == "validate_code"){
      if(Voucher::where('code',$request->code)->count() == 0){
        return json_encode(true);
      }else{
        return json_encode(false);
      }
    }else if($request->type == "create"){
      $result = Voucher::create(['name'=>$request->name,'code'=>$request->code,'type'=>$request->dis_type,'amount'=>$request->amount,'active'=>1,'creator_id'=>$user->id,'creator_name'=>$user->name]);
      if($result){
        return json_encode(true);
      }else{
        return json_encode(false);
      }
    }else if($request->type == "read"){
      $voucher = Voucher::where('id',$request->id)->first();
      return json_encode($voucher);
    }else if($request->type == "edit"){
      $result = Voucher::where('code',$request->code)->update(['name'=>$request->name,'type'=>$request->dis_type,'amount'=>$request->amount]);
      return json_encode($result);
    }else if($request->type == "status"){
      $result = Voucher::where('id',$request->id)->update(['active'=>$request->status]);
      return json_encode($result);
    }else if($request->type == "delete"){
      $result = Voucher::where('id',$request->id)->delete();
      return json_encode($result);
    }

  }

  public function postDeleteProduct(Request $request)
  {
    if(Auth::check()){

      Product_list::where('barcode',$request->barcode)->update(['deleted_by'=>Auth::user()->name]);
      Product_list::where('barcode',$request->barcode)->delete();
      Branch_product::where('barcode',$request->barcode)->update(['product_sync'=>0]);
      Branch_product::where('barcode',$request->barcode)->delete();
      Warehouse_stock::where('barcode',$request->barcode)->delete();

      return json_encode(true);
    }else{
      return json_encode(false);
    }
  }

  public function getImport()
  {
    return view('product_import');
  }

  // public function postImport(Request $request)
  // {
  //   $branch = Branch::get();
  //   $file = $request->file('product_list');
  //   $path = "storage/import";
  //   $target = $path."/".$file->getClientOriginalName();
  //   $file->move($path,$file->getClientOriginalName());

  //   $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($target);
  //   $reader->setReadDataOnly(true);
  //   $spreadsheet = $reader->load($target);
  //   $spreadsheet = $spreadsheet->getActiveSheet();
  //   $data = $spreadsheet->toArray();

  //   foreach($data as $index => $result){
  //     $counter = Product_list::where('barcode',$result[0])->count();

  //     //if barcode doesn't exist
  //     if($counter == 0){
  //       Product_list::create([
  //                     'department'=>1,
  //                     'category_id'=>1,
  //                     'barcode'=>$result[0],
  //                     'product_name'=>$result[1],
  //                     'uom'=>null,
  //                     'cost'=>$result[2],
  //                     'price'=>$result[3],
  //                     'quantity'=>0,
  //                     'reorder_level'=>null,
  //                     'recommend_quantity'=>null,
  //                     'product_sync'=>0,
  //                   ]);

  //       Warehouse_stock::create([
  //                     'department'=>1,
  //                     'category_id'=>1,
  //                     'barcode'=>$result[0],
  //                     'product_name'=>$result[1],
  //                     'uom'=>null,
  //                     'cost'=>$result[2],
  //                     'price'=>$result[3],
  //                     'quantity'=>0,
  //                     'reorder_level'=>null,
  //                     'recommend_quantity'=>null,
  //                     'product_sync'=>null,
  //                   ]);

  //       foreach($branch as $a){
  //         Branch_product::create([
  //                     'branch_id' => $a->id,
  //                     'department'=>1,
  //                     'category_id'=>1,
  //                     'barcode'=>$result[0],
  //                     'product_name'=>$result[1],
  //                     'uom'=>null,
  //                     'cost'=>$result[2],
  //                     'price'=>$result[3],
  //                     'quantity'=>null,
  //                     'reorder_level'=>null,
  //                     'recommend_quantity'=>null,
  //                     'product_sync'=>0,
  //                   ]);
  //       }

  //     }else{
  //       Product_list::where('barcode',$result[0])
  //                     ->update([
  //                       'cost'=>$result[2],
  //                       'price'=>$result[3],
  //                       'product_sync'=>0,
  //                     ]);

  //       Warehouse_stock::where('barcode',$result[0])
  //                     ->update([
  //                     'cost'=>$result[2],
  //                     'price'=>$result[3],
  //                     'product_sync'=>null,
  //                   ]);

  //       Branch_product::where('barcode',$result[0])
  //                   ->update([
  //                     'barcode'=>$result[0],
  //                     'cost'=>$result[2],
  //                     'price'=>$result[3],
  //                     'product_sync'=>0,
  //                   ]);
  //     }
  //   }

  //   return json_encode(true);
  // }

  public function getSupplierProduct()
  {
    $url = route('home')."?p=product_menu";

    return view('product.supplier_product',compact('url'));
  }

  public function postSupplierProduct(Request $request)
  {

    dd($request);
  }

}
