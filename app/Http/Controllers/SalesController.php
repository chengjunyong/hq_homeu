<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Branch;
use App\Transaction;
use App\Transaction_detail;
use App\Product_list;
use App\Branch_stock_history;
use App\Branch_product;
use App\Do_list;
use App\Do_detail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SalesController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth', 'user_access']);
  }
    
  public function getSalesReport()
  {
    $url = route('home')."?p=sales_menu";

    $selected_date_from = date('Y-m-d', strtotime(now()));
    $selected_date_to = date('Y-m-d', strtotime(now()));
    
    $branch = Branch::get();

    return view('sales_report',compact('branch', 'selected_date_from', 'selected_date_to', 'url'));
  }

  public function getDailyReport()
  {
    $url = route('home')."?p=sales_menu";

    $selected_date_from = date('Y-m-d', strtotime(now()));
    $selected_date_to = date('Y-m-d', strtotime(now()));
    
    $branch = Branch::get();

    return view('daily_report',compact('branch', 'selected_date_from', 'selected_date_to','url'));
  }

  public function getBranchReport()
  {
    $url = route('home')."?p=sales_menu";

    $selected_date_from = date('Y-m-d', strtotime(now()));
    $selected_date_to = date('Y-m-d', strtotime(now()));
    
    $branch = Branch::get();

    return view('branch_report',compact('branch', 'selected_date_from', 'selected_date_to','url'));
  }

  public function getSalesTransactionReport(Request $request)
  {
    $url = route('home')."?p=sales_menu";

    $selected_branch = null;
    $selected_branch_token = null;
    $selected_date_from = date('Y-m-d', strtotime(now()));
    $selected_date_to = date('Y-m-d', strtotime(now()));

    $date = date('Y-m-d H:i:s', strtotime(now()));
    $user = Auth::user();

    // default
    if($request->branch_token)
    {
      $selected_branch_token = $request->branch_token;
    }

    if($request->report_date_from)
    {
      $selected_date_from = $request->report_date_from;
    }

    if($request->report_date_to)
    {
      $selected_date_to = $request->report_date_to;
    }

    if(!$selected_branch_token)
    {
      $selected_branch = Branch::first();
    }
    else
    {
      $selected_branch = Branch::where('token', $selected_branch_token)->first();
    }

    $selected_date_start = $selected_date_from." 00:00:00";
    $selected_date_end = $selected_date_to." 23:59:59";

    if($selected_branch)
    {
      $transaction = Transaction::whereBetween('transaction_date', [$selected_date_start, $selected_date_end])->where('branch_id', $selected_branch->token)->get();
    }
    else
    {
      $transaction = Transaction::whereBetween('transaction_date', [$selected_date_start, $selected_date_end])->where('branch_id', null)->get();
    }

    return view('sales_report_transaction',compact('selected_branch', 'selected_date_from', 'selected_date_to', 'transaction', 'url', 'date', 'user'));
  }

  public function getSalesReportDetail($branch_id, $branch_transaction_id)
  { 
    $url = route('home')."?p=product_menu";
    $transaction_detail = Transaction_detail::where('branch_id', $branch_id)->where('branch_transaction_id', $branch_transaction_id)->get();

    $date = date('Y-m-d H:i:s', strtotime(now()));
    $user = Auth::user();

    return view('sales_report_detail',compact('transaction_detail', 'url', 'date', 'user'));
  }

  public function getdailyReportDetail(Request $request)
  {
    $report_date_from = $request->report_date_from;
    $report_date_to = $request->report_date_to;

    $url = route('home')."?p=sales_menu";

    $selected_branch = [];
    $selected_branch_token = [];
    $selected_date_from = date('Y-m-d', strtotime(now()));
    $selected_date_to = date('Y-m-d', strtotime(now()));

    $date = date('Y-m-d H:i:s', strtotime(now()));
    $user = Auth::user();

    // default
    $branch = $request->branch;

    if($request->branch)
    {
      if(count($branch) > 0)
      {
        $selected_branch = Branch::whereIn('token', $branch)->get();
      }
    }

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

    if(count($selected_branch) > 0)
    {
      foreach($selected_branch as $branch_detail)
      {
        $transaction_detail_list = Transaction_detail::whereBetween('created_at', [$selected_date_start, $selected_date_end])->where('branch_id', $branch_detail->token)->get();

        $barcode_array = [];
        foreach($transaction_detail_list as $transaction_detail)
        {
          if(!in_array($transaction_detail->barcode, $barcode_array))
          {
            array_push($barcode_array, $transaction_detail->barcode);
          }
        }

        $product_list = Product_list::whereIn('product_list.barcode', $barcode_array)->leftJoin('category', 'product_list.category_id', '=', 'category.id')->select('product_list.*', 'category.department_id', 'category.category_name')->get();

        $category_id_array = [];
        $category_report_array = [];

        foreach($product_list as $product_detail)
        {
          if(!in_array($product_detail->category_id, $category_id_array))
          {
            array_push($category_id_array, $product_detail->category_id);

            $category_report = new \stdClass();
            $category_report->category_id = $product_detail->category_id;
            $category_report->category_name = $product_detail->category_name;
            $category_report->total = 0;
            $category_report->quantity = 0;

            array_push($category_report_array, $category_report);
          }
        }

        foreach($transaction_detail_list as $transaction_detail)
        {
          $category_id = null;
          foreach($product_list as $product_detail)
          {
            if(str_replace(" ", "", $product_detail->barcode) == str_replace(" ", "", $transaction_detail->barcode))
            {
              $category_id = $product_detail->category_id;
              break;
            }
          }

          if($category_id)
          {
            foreach($category_report_array as $key => $category_report)
            {
              if($category_report->category_id == $category_id)
              {
                $category_report_array[$key]->total = $category_report_array[$key]->total + $transaction_detail->total;
                $category_report_array[$key]->quantity = $category_report_array[$key]->quantity + $transaction_detail->quantity;
                break;
              }
            }
          }
        }

        $branch_detail->category_report = $category_report_array;
      }
    }

    return view('daily_report_detail',compact('selected_branch', 'selected_date_from', 'selected_date_to', 'url', 'date', 'user'));
  }

  public function getBranchReportDetail(Request $request)
  {
    $report_date_from = $request->report_date_from;
    $report_date_to = $request->report_date_to;

    $url = route('home')."?p=sales_menu";

    $selected_branch = [];
    $selected_branch_token = [];
    $selected_date_from = date('Y-m-d', strtotime(now()));
    $selected_date_to = date('Y-m-d', strtotime(now()));

    $date = date('Y-m-d H:i:s', strtotime(now()));
    $user = Auth::user();

    // default
    $branch = $request->branch;

    if($request->branch)
    {
      if(count($branch) > 0)
      {
        $selected_branch = Branch::whereIn('token', $branch)->get();
      }
    }

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

    if(count($selected_branch) > 0)
    {
      $transaction_detail_list = Transaction_detail::whereBetween('created_at', [$selected_date_start, $selected_date_end])->whereIn('branch_id', $branch)->selectRaw('*, sum(total) as branch_total')->groupBy('branch_id')->get();

      foreach($selected_branch as $branch)
      {
        $branch->branch_total = 0;
        foreach($transaction_detail_list as $transaction_detail)
        {
          if($transaction_detail->branch_id == $branch->token)
          {
            $branch->branch_total = $transaction_detail->branch_total;
            break;
          }
        }
      }
    }

    return view('branch_report_detail',compact('selected_branch', 'selected_date_from', 'selected_date_to', 'url', 'date', 'user'));
  }

  public function exportSalesReport(Request $request)
  {
    if(!Storage::exists('public/report'))
    {
      Storage::makeDirectory('public/report', 0775, true); //creates directory
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'INVOICE NO');
    $sheet->setCellValue('B1', 'PAYMENT TYPE');
    $sheet->setCellValue('C1', 'REFERENCE NO');
    $sheet->setCellValue('D1', 'SUBTOTAL(RM)');
    $sheet->setCellValue('E1', 'DISCOUNT(RM)');
    $sheet->setCellValue('F1', 'TOTAL(RM)');
    $sheet->setCellValue('G1', 'RECEIVED PAYMENT(RM)');
    $sheet->setCellValue('H1', 'BALANCE(RM)');
    $sheet->setCellValue('I1', 'TRANSACTION DATE');

    $writer = new Xlsx($spreadsheet);
    $path = 'storage/report/sales report.xlsx';
    $writer->save($path);

    return response()->download($path);
  }

  public function getStockBalance()
  {
    $url = route('home')."?p=sales_menu";

    $branch = Branch::get();

    return view('stock_balance',compact('url','branch'));
  }

  public function postStockBalanceReport(Request $request)
  {
    $date = date('Y-m-d H:i:s', strtotime(now()));
    $stock = Branch_product::join('department','department.id','=','branch_product.department_id')
                          ->join('category','category.id','=','branch_product.category_id')
                          ->where('branch_product.branch_id',$request->branch_id)
                          ->select('department.department_name','category.category_name','branch_product.*')
                          ->limit(10000)
                          ->get();

    $branch = Branch::where('id',$request->branch_id)->first();

    $balance_stock = Branch_product::where('branch_id',$request->branch_id)
                                    ->selectRaw('SUM(cost * quantity) as total')
                                    ->get();

    return view('report.stock_balance_report',compact('stock','branch','date','balance_stock'));
  }

  public function exportStockBalance(Request $request)
  {

     if(!Storage::exists('public/report'))
    {
      Storage::makeDirectory('public/report', 0775, true); //creates directory
    }

    $date = date('Y-m-d', strtotime(now()));
    $branch = Branch::where('id',$request->branch_id)->first();

    $stock = Branch_product::join('department','department.id','=','branch_product.department_id')
                          ->join('category','category.id','=','branch_product.category_id')
                          ->where('branch_product.branch_id',$request->branch_id)
                          ->select('department.department_name','category.category_name','branch_product.*')
                          ->get();

    $stock_array = array();
    foreach($stock as $key => $result){
      array_push($stock_array,[$key+1,$result->department_name,$result->category_name,$result->barcode,$result->product_name,$result->cost,number_format($result->quantity,0),$result->price,number_format($result->cost * $result->quantity,2)]);
    }

    $balance_stock = Branch_product::where('branch_id',$request->branch_id)
                                    ->selectRaw('SUM(cost * quantity) as total')
                                    ->get();

    $spreadsheet = new Spreadsheet();
    $spreadsheet->getActiveSheet()->mergeCells('A1:I1');
    $spreadsheet->getActiveSheet()->mergeCells('A2:I2');
    $spreadsheet->getActiveSheet()->mergeCells('G4:H4');
    $spreadsheet->getActiveSheet()->fromArray($stock_array,null,'A6');  
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');

    //Header
    $sheet->setCellValue('A1', 'HomeU (M) Sdh Bhd');
    $sheet->setCellValue('A2', 'Stock Balance Stock');
    $sheet->setCellValue('A3', 'Date');
    $sheet->setCellValue('B3', $date);
    $sheet->setCellValue('A4', 'Branch');
    $sheet->setCellValue('B4', $branch->branch_name);
    $sheet->setCellValue('G4', 'Balance Stock');
    $sheet->setCellValue('I4', number_format($balance_stock[0]->total,2));

    //Data
    $sheet->setCellValue('A5', 'Bil');
    $sheet->setCellValue('B5', 'Department');
    $sheet->setCellValue('C5', 'Category');
    $sheet->setCellValue('D5', 'Barcode');
    $sheet->setCellValue('E5', 'Product Name');
    $sheet->setCellValue('F5', 'Cost');
    $sheet->setCellValue('G5', 'Quantity');
    $sheet->setCellValue('H5', 'Selling Price');
    $sheet->setCellValue('I5', 'Total Cost');

    $writer = new Xlsx($spreadsheet);
    $path = 'storage/report/Stock Balance Report.xlsx';
    $writer->save($path);

    return response()->json($path);
  }

  public function getStockOrder()
  {
    $url = route('home')."?p=sales_menu";

  }

  public function getStockReorderReport(Request $request)
  {

  }
}
