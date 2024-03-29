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
use App\Hamper;
use App\Product_history;
use App\user_access_control;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
  public function __construct()
  {
     // dd($this->middleware(['auth', 'user_access']));
  }
    
  public function getProductList()
  { 
    $access = app('App\Http\Controllers\UserController')->checkAccessControl();
    $permission = explode(",",user_access_control::where('user_id',auth()->id())->first()->access_control);

    $permission = in_array(35,$permission);

    $url = route('home')."?p=product_menu";

    $search = null;
  	$product_list = Product_list::join('department','department.id','=','product_list.department_id')
                                ->join('category','category.id','=','product_list.category_id')
                                ->select('product_list.*','department.department_name','category.category_name')
                                ->paginate(14);

  	return view('product_list',compact('product_list','search','url','access','permission'));
  }

  public function searchProduct(Request $request)
  {
    $access = app('App\Http\Controllers\UserController')->checkAccessControl();
    $permission = explode(",",user_access_control::where('user_id',auth()->id())->first()->access_control);

    $permission = in_array(35,$permission);

    $url = route('home')."?p=product_menu";

    $search = $request->search;

  	$product_list = Product_list::join('department','department.id','=','product_list.department_id')
                            ->join('category','category.id','=','product_list.category_id')
                            ->select('product_list.*','department.department_name','category.category_name')
                            ->where('product_list.barcode','LIKE','%'.$request->search.'%')
  													->orWhere('product_list.product_name','LIKE','%'.$request->search.'%')
  													->paginate(14);

  	$product_list->withPath('?search='.$request->search);

  	return view('product_list',compact('product_list','search','url','access','permission'));
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
        [
          'barcode'=>$request->barcode,
          'branch_id'=>$result->id,
        ],
        [
        'department_id'=>$request->department,
        'category_id'=>$request->category,
        'product_name'=>$request->product_name,
        'uom'=>$request->uom,
        'measurement'=>$request->measurement,
        'cost'=>$request->cost,
        'price'=>$request->price,
        'quantity'=>0,
        'reorder_level'=>0,
        'recommend_quantity'=>0,
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
      'reorder_level'=>0,
      'recommend_quantity'=>0,
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
      'reorder_level'=>0,
      'reorder_quantity'=>0,
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
    $access = app('App\Http\Controllers\UserController')->checkAccessControl();

    if(!$access)
    {
      return view('not_access');
    }

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

    $supplier = Supplier::whereNotIn('id',array_column($existing_supplier,'s_id'))->get();

    $history = Product_history::where('product_id',$product->id)->orderBy('created_at','DESC')->limit(100)->get();

    return view('modifyproduct',compact('product','department','category','default_price','url','supplier','supplier_list','history'));
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
                  ]);

    return json_encode(true);
  }

  public function postModifyProduct(Request $request)
  {
    $previous = Product_list::where('barcode',$request->barcode)->first();
    if($request->cost != $previous->cost || $request->price != $previous->price){
      $cvalue = "Cost : ".number_format($request->cost,2)."<br/>Price : ".number_format($request->price,2);
      $pvalue = "Cost : ".number_format($previous->cost,2)."<br/>Price : ".number_format($previous->price,2);
      Product_history::create([
                        'product_id' => $previous->id,
                        'barcode' => $previous->barcode,
                        'previous_value' => $pvalue,
                        'current_value' => $cvalue,
                        'created_by' => Auth::user()->id,
                        'creator_name' => Auth::user()->name,
                      ]); 
    }

    Branch_product::where('barcode',$request->barcode)
                    ->update([
                      'department_id'=>$request->department,
                      'category_id'=>$request->category,
                      'product_name'=>$request->product_name,
                      'uom'=>$request->uom,
                      'measurement'=>$request->measurement,
                      'cost'=>$request->cost,
                      'price'=>$request->price,
                      'normal_wholesale_price'=>$request->normal_wholesales_price ?? null,
                      'normal_wholesale_quantity'=>$request->normal_wholesales_quantity ?? null,
                      'normal_wholesale_price2'=>$request->normal_wholesales_price2 ?? null,
                      'normal_wholesale_quantity2'=>$request->normal_wholesales_quantity2 ?? null,
                      'normal_wholesale_price3'=>$request->normal_wholesales_price3 ?? null,
                      'normal_wholesale_quantity3'=>$request->normal_wholesales_quantity3 ?? null,
                      'normal_wholesale_price4'=>$request->normal_wholesales_price4 ?? null,
                      'normal_wholesale_quantity4'=>$request->normal_wholesales_quantity4 ?? null,
                      'normal_wholesale_price5'=>$request->normal_wholesales_price5 ?? null,
                      'normal_wholesale_quantity5'=>$request->normal_wholesales_quantity5 ?? null,
                      'normal_wholesale_price6'=>$request->normal_wholesales_price6 ?? null,
                      'normal_wholesale_quantity6'=>$request->normal_wholesales_quantity6 ?? null,
                      'normal_wholesale_price7'=>$request->normal_wholesales_price7 ?? null,
                      'normal_wholesale_quantity7'=>$request->normal_wholesales_quantity7 ?? null,
                      // 'reorder_level'=>$request->reorder_level ?? null,
                      // 'recommend_quantity'=>$request->recommend_quantity ?? null,
                      'promotion_start'=>$request->promotion_start ?? null,
                      'promotion_end'=>$request->promotion_end ?? null,
                      'promotion_price'=>$request->promotion_price ?? null,
                      'wholesale_price'=>$request->wholesales_price ?? null,
                      'wholesale_quantity'=>$request->wholesales_quantity ?? null,
                      'wholesale_price2'=>$request->wholesales_price2 ?? null,
                      'wholesale_quantity2'=>$request->wholesales_quantity2 ?? null,
                      'wholesale_price'=>$request->wholesales_price ?? null,
                      'wholesale_quantity'=>$request->wholesales_quantity ?? null,
                      'wholesale_price2'=>$request->wholesales_price2 ?? null,
                      'wholesale_quantity2'=>$request->wholesales_quantity2 ?? null,
                      'wholesale_price3'=>$request->wholesales_price3 ?? null,
                      'wholesale_quantity3'=>$request->wholesales_quantity3 ?? null,
                      'wholesale_price4'=>$request->wholesales_price4 ?? null,
                      'wholesale_quantity4'=>$request->wholesales_quantity4 ?? null,
                      'wholesale_price5'=>$request->wholesales_price5 ?? null,
                      'wholesale_quantity5'=>$request->wholesales_quantity5 ?? null,
                      'wholesale_price6'=>$request->wholesales_price6 ?? null,
                      'wholesale_quantity6'=>$request->wholesales_quantity6 ?? null,
                      'wholesale_price7'=>$request->wholesales_price7 ?? null,
                      'wholesale_quantity7'=>$request->wholesales_quantity7 ?? null,
                      'wholesale_start_date'=>$request->wholesales_start ?? null,
                      'wholesale_end_date'=>$request->wholesales_end ?? null,
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
                      'normal_wholesale_price'=>$request->normal_wholesales_price ?? null,
                      'normal_wholesale_quantity'=>$request->normal_wholesales_quantity ?? null,
                      'normal_wholesale_price2'=>$request->normal_wholesales_price2 ?? null,
                      'normal_wholesale_quantity2'=>$request->normal_wholesales_quantity2 ?? null,
                      'normal_wholesale_price3'=>$request->normal_wholesales_price3 ?? null,
                      'normal_wholesale_quantity3'=>$request->normal_wholesales_quantity3 ?? null,
                      'normal_wholesale_price4'=>$request->normal_wholesales_price4 ?? null,
                      'normal_wholesale_quantity4'=>$request->normal_wholesales_quantity4 ?? null,
                      'normal_wholesale_price5'=>$request->normal_wholesales_price5 ?? null,
                      'normal_wholesale_quantity5'=>$request->normal_wholesales_quantity5 ?? null,
                      'normal_wholesale_price6'=>$request->normal_wholesales_price6 ?? null,
                      'normal_wholesale_quantity6'=>$request->normal_wholesales_quantity6 ?? null,
                      'normal_wholesale_price7'=>$request->normal_wholesales_price7 ?? null,
                      'normal_wholesale_quantity7'=>$request->normal_wholesales_quantity7 ?? null,
                      // 'reorder_level'=>$request->reorder_level ?? null,
                      // 'recommend_quantity'=>$request->recommend_quantity ?? null,
                      'schedule_date'=>$request->schedule_date,
                      'schedule_price'=>$request->schedule_price,
                      'promotion_start'=>$request->promotion_start ?? null,
                      'promotion_end'=>$request->promotion_end ?? null,
                      'promotion_price'=>$request->promotion_price ?? null,
                      'wholesale_price'=>$request->wholesales_price ?? null,
                      'wholesale_quantity'=>$request->wholesales_quantity ?? null,
                      'wholesale_price2'=>$request->wholesales_price2 ?? null,
                      'wholesale_quantity2'=>$request->wholesales_quantity2 ?? null,
                      'wholesale_price'=>$request->wholesales_price ?? null,
                      'wholesale_quantity'=>$request->wholesales_quantity ?? null,
                      'wholesale_price2'=>$request->wholesales_price2 ?? null,
                      'wholesale_quantity2'=>$request->wholesales_quantity2 ?? null,
                      'wholesale_price3'=>$request->wholesales_price3 ?? null,
                      'wholesale_quantity3'=>$request->wholesales_quantity3 ?? null,
                      'wholesale_price4'=>$request->wholesales_price4 ?? null,
                      'wholesale_quantity4'=>$request->wholesales_quantity4 ?? null,
                      'wholesale_price5'=>$request->wholesales_price5 ?? null,
                      'wholesale_quantity5'=>$request->wholesales_quantity5 ?? null,
                      'wholesale_price6'=>$request->wholesales_price6 ?? null,
                      'wholesale_quantity6'=>$request->wholesales_quantity6 ?? null,
                      'wholesale_price7'=>$request->wholesales_price7 ?? null,
                      'wholesale_quantity7'=>$request->wholesales_quantity7 ?? null,
                      'wholesale_start_date'=>$request->wholesales_start ?? null,
                      'wholesale_end_date'=>$request->wholesales_end ?? null,
                      'product_sync'=>0,
                      'remark' => $request->remark,
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
                      // 'reorder_level'=>0,
                      // 'reorder_quantity'=>0,
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

    $supplier_list = Supplier::orderBy('supplier_name')->get();

    return view('product.supplier_product',compact('url', 'supplier_list'));
  }

  public function getSupplierProductReport()
  {
    $date = date('Y-m-d H:i:s');
    $user = Auth::user();

    $supplier_id = $_GET['supplier_id'];

    $product_supplier = Product_supplier::where('product_supplier.supplier_id', $supplier_id)->leftJoin('product_list', 'product_list.id', '=', 'product_supplier.product_id')->leftJoin('department', 'department.id', '=', 'product_list.department_id')->leftJoin('category', 'category.id', '=', 'product_list.category_id')->select('department.department_name', 'category.category_name', 'product_list.barcode', 'product_list.product_name', 'product_list.measurement', 'product_list.cost', 'product_list.price', 'product_list.quantity', 'product_list.created_at', 'product_list.updated_at')->get();

    return view('report.supplier_product_report',compact('date', 'user', 'product_supplier'));
  }

  public function exportSupplierProductReport(Request $request)
  {
    if(!Storage::exists('public/report'))
    {
      Storage::makeDirectory('public/report', 0775, true); //creates directory
    }

    $supplier_id = $request->supplier_id;

    $supplier = Supplier::where('id', $supplier_id)->first();
    $product_supplier = Product_supplier::where('product_supplier.supplier_id', $supplier_id)->leftJoin('product_list', 'product_list.id', '=', 'product_supplier.product_id')->leftJoin('department', 'department.id', '=', 'product_list.department_id')->leftJoin('category', 'category.id', '=', 'product_list.category_id')->select('department.department_name', 'category.category_name', 'product_list.barcode', 'product_list.product_name', 'product_list.measurement', 'product_list.cost', 'product_list.price', 'product_list.quantity', 'product_list.created_at', 'product_list.updated_at')->get();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setCellValue('A1', 'Home U (M) Sdn Bhd');
    $sheet->setCellValue('A2', 'Supplier Product List');

    $sheet->mergeCells('A1:J1');
    $sheet->mergeCells('A2:J2');

    $sheet->setCellValue('A3', 'Supplier:');
    $sheet->setCellValue('B3', $supplier->supplier_name);

    $sheet->setCellValue('I3', 'Generate Date:');
    $sheet->setCellValue('J3', date('d-m-Y', strtotime(now())));

    $sheet->getStyle("A1:J3")->getAlignment()->setWrapText(true);

    $sheet->setCellValue('A5', 'No');
    $sheet->setCellValue('B5', 'Department');
    $sheet->setCellValue('C5', 'Category');
    $sheet->setCellValue('D5', 'Barcode');
    $sheet->setCellValue('E5', 'Product name');
    $sheet->setCellValue('F5', 'Measurement');
    $sheet->setCellValue('G5', 'Cost');
    $sheet->setCellValue('H5', 'Price');
    $sheet->setCellValue('I5', 'Stock');
    $sheet->setCellValue('J5', 'Last updated');

    $started_row = 6;
    foreach($product_supplier as $key => $product)
    {
      $sheet->setCellValue('A'.$started_row, $key);
      $sheet->setCellValue('B'.$started_row, $product->department_name);
      $sheet->setCellValue('C'.$started_row, $product->category_name);
      $sheet->setCellValue('D'.$started_row, $product->barcode);
      $sheet->setCellValue('E'.$started_row, $product->product_name);
      $sheet->setCellValue('F'.$started_row, $product->measurement);
      $sheet->setCellValue('G'.$started_row, number_format($product->cost, 2));
      $sheet->setCellValue('H'.$started_row, number_format($product->price, 2));
      $sheet->setCellValue('I'.$started_row, $product->quantity);
      $sheet->setCellValue('J'.$started_row, $product->updated_at);

      $started_row++;
    }

    $sheet->getColumnDimension('A')->setWidth(10);
    $sheet->getColumnDimension('B')->setWidth(20);
    $sheet->getColumnDimension('C')->setWidth(20);
    $sheet->getColumnDimension('D')->setWidth(20);
    $sheet->getColumnDimension('E')->setWidth(25);
    $sheet->getColumnDimension('F')->setWidth(15);
    $sheet->getColumnDimension('G')->setWidth(15);
    $sheet->getColumnDimension('H')->setWidth(15);
    $sheet->getColumnDimension('I')->setWidth(15);
    $sheet->getColumnDimension('J')->setWidth(20);

    $sheet->getStyle('A5:J'.($started_row - 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

    $writer = new Xlsx($spreadsheet);
    $path = 'storage/report/Supplier Product Report.xlsx';
    $writer->save($path);

    return response()->download($path);
  }

  public function getHamperList()
  {
    $url = route('home')."?p=product_menu";

    $hamper = Hamper::with('user','branch')
                      ->orderBy('updated_at','desc')
                      ->paginate('15');

    $branches = Branch::whereNotIn('id',['11','12'])->get();

    return view('product.hamper',compact('url','hamper','branches'));
  }

  public function ajaxAddHamperProduct(Request $request)
  {

    $product = Product_list::where('barcode',$request->barcode)->select('product_name','barcode')->first();
    if($product == null){
      $result = 'null';
    }else{
      $result = $product;
    }

    return json_encode($result);
  }

  public function getCreateHamper(Request $request)
  {
    $product_list = json_decode($request->product_list);

    $err = new \stdClass();
    $user = Auth::user();
    $count = Hamper::where('barcode',$request->barcode)->count();
    if($count != 0){
      $err->result = false;
      $err->msg = "Barcode Exist"; 
      return json_encode($err);
    }else{
      Hamper::create([
            'branch_id' => $request->branch,
            'name' => $request->name,
            'price' => $request->price,
            'barcode' => $request->barcode,
            'product_list' => $request->product_list,
            'quantity' => $request->quantity,
            'created_by' => $user->id,
      ]);

      $branches = Branch::all();
      foreach($branches as $branch){
        Branch_product::where('id',$branch->id)
                      ->updateOrCreate(
                        [
                          'barcode' => $request->barcode,
                        ],[
                          'branch_id' => $branch->id,
                          'department_id' => 1,
                          'category_id' => 1,
                          'product_name' => $request->name,
                          'uom' => NULL,
                          'measurement' => 'unit',
                          'cost' => 0,
                          'price' => $request->price,
                          'quantity' => $request->branch == $branch->id ? $request->quantity : 0,
                          'product_sync' => 0,
                        ]);
      }

      Product_list::updateOrCreate(
      [
        'barcode' => $request->barcode,
      ],[
        'department_id' => 1,
        'category_id' => 1,
        'product_name' => $request->name,
        'uom' => NULL,
        'measurement' => 'unit',
        'cost' => 0,
        'price' => $request->price,
        'quantity' => $request->quantity,
        'product_sync' => 0,
      ]);

      Warehouse_stock::updateOrCreate(
      [
        'barcode' => $request->barcode,
      ],[
        'department_id' => 1,
        'category_id' => 1,
        'product_name' => $request->name,
        'uom' => NULL,
        'measurement' => 'unit',
        'cost' => 0,
        'price' => $request->price,
        'quantity' => $request->branch == '0' ? $request->quantity : 0,
      ]);

      $product_list = json_decode($request->product_list);
      if($request->branch == '0'){
        foreach($product_list as $product){
          Warehouse_stock::where('barcode',$product->barcode)
                          ->decrement('quantity',$product->quantity * $request->quantity);
        }
      }else{
        foreach($product_list as $product){
          Branch_product::where('branch_id',$request->branch)
                          ->where('barcode',$product->barcode)
                          ->decrement('quantity',$product->quantity * $request->quantity);
        }
      }

      $err->result = true;
      $err->msg = "Create Successful";
    }

    return json_encode($err);
  }

  public function ajaxDeleteHamper(Request $request)
  {
    $hamper = Hamper::where('id',$request->id)->first();

    Product_list::where('barcode',$hamper->barcode)->delete();
    
    if($hamper->branch_id == '0'){
      $remain_qty = Warehouse_stock::where('barcode',$hamper->barcode)
                                    ->first();
    }else{
      $remain_qty = Branch_product::where('branch_id',$hamper->branch_id)
                                    ->where('barcode',$hamper->barcode)
                                    ->first();
    }

    $remain_qty = $remain_qty->quantity ?? 0;
    $product_list = json_decode($hamper->product_list);
    if($hamper->branch_id == '0'){
      foreach($product_list as $product){
        Warehouse_stock::where('barcode',$product->barcode)
                        ->increment('quantity',$product->quantity * $remain_qty);
      }

      Warehouse_stock::where('barcode',$hamper->barcode)->delete();
    }else{
      foreach($product_list as $product){
        Branch_product::where('branch_id',$hamper->branch_id)
                        ->where('barcode',$product->barcode)
                        ->increment('quantity',$product->quantity * $remain_qty);
      }

      Branch_product::where('barcode',$hamper->barcode)->update(['product_sync' => 0]);
      Branch_product::where('barcode',$hamper->barcode)->delete();
    }

    $hamper->delete();

    return json_encode(true);
  }

  public function getHamper(Request $request)
  {

    $hamper = Hamper::where('id',$request->id)->first();

    return json_encode($hamper);
  }

  public function getEditHamper(Request $request)
  {
    $err = new \stdClass();
    $user = Auth::user();

    $hamper = Hamper::where('id',$request->id)->first();
    $hamper->update([
                  'name' => $request->name,
                  'price' => $request->price,
                  'created_by' => $user->id,
            ]);

    Branch_product::where('barcode',$hamper->barcode)
                    ->update([
                      'product_name' => $hamper->name,
                      'price' => $hamper->price,
                      'product_sync' => 0,
                    ]);

    Product_list::where('barcode',$hamper->barcode)
                  ->update([
                    'product_name' => $hamper->name,
                    'price' => $hamper->price,
                  ]);

    Warehouse_stock::where('barcode',$hamper->barcode)
                    ->update([
                      'product_name' => $hamper->name,
                      'price' => $hamper->price,
                    ]);

    $err->result = true;
    $err->msg = "Update Successful";
    
    return json_encode($err);
  }

  public function printHamper(Request $request)
  {
    $hamper = Hamper::find($request->id);
    $list = collect(json_decode($hamper->product_list));

    $products = Product_list::whereIn('barcode',$list->pluck('barcode'))->get();

    return view('product.hamper_print',compact('hamper','products','list'));

  }
}
