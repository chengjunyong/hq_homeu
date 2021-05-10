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
use Illuminate\Support\Facades\DB;

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

    return view('sales_report',compact('selected_date_from', 'selected_date_to', 'url'));
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
    $report_date_from = $request->report_date_from;
    $report_date_to = $request->report_date_to;

    $url = route('home')."?p=sales_menu";

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

    $branch_list = Branch::get();

    $cash_total = 0;
    $credit_card_total = 0;
    $tng_total = 0;
    $other_total = 0;
    $credit_sales_total = 0;
    $total = 0;

    foreach($branch_list as $branch)
    { 
      $branch->cash = 0;
      $branch->credit_card = 0;
      $branch->tng = 0;
      $branch->other = 0;
      $branch->credit_sales = 0;

      $branch_other_total = 0;
      $branch_total = 0;

      $transaction = Transaction::whereBetween('transaction_date', [$selected_date_start, $selected_date_end])->where('branch_id', $branch->token)->selectRaw('*, sum(total) as payment_type_total')->groupBy('payment_type')->get();

      foreach($transaction as $value)
      {
        $branch_total += $value->payment_type_total;

        $total += $value->payment_type_total;

        if($value->payment_type == "cash")
        {
          $branch->cash = $value->payment_type_total;

          $cash_total += $value->payment_type_total;
        }
        elseif($value->payment_type == "credit_card")
        {
          $branch->credit_card = $value->payment_type_total;

          $credit_card_total += $value->payment_type_total;
        }
        elseif($value->payment_type == "tng")
        {
          $branch->tng = $value->payment_type_total;

          $tng_total += $value->payment_type_total;
        }
        else
        {
          $branch_other_total += $value->payment_type_total;

          $other_total += $value->payment_type_total;
        }
      }

      if($branch_other_total > 0)
      {
        $branch->other = $branch_other_total;
      }
    }

    $total_summary = new \stdClass();
    $total_summary->cash = $cash_total;
    $total_summary->credit_card = $credit_card_total;
    $total_summary->tng = $tng_total;
    $total_summary->other = $other_total;
    $total_summary->credit_sales = $credit_sales_total;
    $total_summary->total = $total;

    return view('sales_report_transaction',compact('selected_date_from', 'selected_date_to', 'branch_list', 'total_summary', 'url', 'date', 'user'));
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
    $url = route('home')."?p=sales_menu";

    $date = date('Y-m-d H:i:s', strtotime(now()));
    $user = Auth::user();

    $selected_date_from = date('Y-m-d', strtotime(now()));
    $selected_date_to = date('Y-m-d', strtotime(now()));

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

    $branch = Branch::where('token', $request->branch)->first();

    $branch_cash = 0;
    $branch_credit_card = 0;
    $branch_tng = 0;
    $branch_other = 0;
    $branch_credit_sales = 0;
    $branch_total = 0;

    $cashier_transaction = Transaction::whereBetween('transaction_date', [$selected_date_start, $selected_date_end])->where('branch_id', $branch->token)->groupBy('ip')->get();

    foreach($cashier_transaction as $cashier)
    {
      $cashier->cash = 0;
      $cashier->credit_card = 0;
      $cashier->tng = 0;
      $cashier->other = 0;
      $cashier->credit_sales = 0;
      $cashier->total = 0;

      $cashier_other_total = 0;
      $cashier_total = 0;

      $transaction = Transaction::whereBetween('transaction_date', [$selected_date_start, $selected_date_end])->where('branch_id', $branch->token)->where('ip', $cashier->ip)->selectRaw('*, sum(total) as payment_type_total')->groupBy('payment_type')->get();

      foreach($transaction as $value)
      {
        $cashier_total += $value->payment_type_total;
        $branch_total += $value->payment_type_total;

        if($value->payment_type == "cash")
        {
          $cashier->cash = $value->payment_type_total;
          $branch_cash += $value->payment_type_total;
        }
        elseif($value->payment_type == "credit_card")
        {
          $cashier->credit_card = $value->payment_type_total;
          $branch_credit_card += $value->payment_type_total;
        }
        elseif($value->payment_type == "tng")
        {
          $cashier->tng = $value->payment_type_total;
          $branch_tng += $value->payment_type_total;
        }
        else
        {
          $cashier_other_total += $value->payment_type_total;
          $branch_other += $value->payment_type_total;
        }
      }

      if($cashier_other_total > 0)
      {
        $cashier->other = $cashier_other_total;
      }

      $cashier->total = $cashier_total;
    }

    $total_summary = new \stdClass();
    $total_summary->cash = $branch_cash;
    $total_summary->credit_card = $branch_credit_card;
    $total_summary->tng = $branch_tng;
    $total_summary->other = $branch_other;
    $total_summary->credit_sales = $branch_credit_sales;
    $total_summary->total = $branch_total;

    return view('branch_report_detail',compact('cashier_transaction', 'total_summary', 'branch', 'selected_date_from', 'selected_date_to', 'url', 'date', 'user'));
  }

  public function exportSalesReport(Request $request)
  {
    if(!Storage::exists('public/report'))
    {
      Storage::makeDirectory('public/report', 0775, true); //creates directory
    }

    $selected_date_from = date('Y-m-d', strtotime(now()));
    $selected_date_to = date('Y-m-d', strtotime(now()));

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

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setCellValue('A1', 'Home U (M) Sdn Bhd');
    $sheet->setCellValue('A2', 'Total Sales Report');

    $sheet->mergeCells('A1:G1');
    $sheet->mergeCells('A2:G2');

    $sheet->setCellValue('A3', 'Date:');
    $sheet->setCellValue('B3', date('d-m-Y', strtotime(now())));

    $sheet->setCellValue('B5', 'Cash');
    $sheet->setCellValue('C5', 'Credit Card');
    $sheet->setCellValue('D5', 'T & Go');
    $sheet->setCellValue('E5', 'Other');
    $sheet->setCellValue('F5', 'Credit Sales');
    $sheet->setCellValue('G5', 'Total');

    $sheet->getStyle('A1:G2')->getAlignment()->setHorizontal('center');

    $branch_list = Branch::get();

    $started_row = 6;

    $cash_total = 0;
    $credit_card_total = 0;
    $tng_total = 0;
    $other_total = 0;
    $credit_sales_total = 0;
    $total = 0;

    foreach($branch_list as $branch)
    { 
      $branch->cash = 0;
      $branch->credit_card = 0;
      $branch->tng = 0;
      $branch->other = 0;
      $branch->credit_sales = 0;

      $branch_other_total = 0;
      $branch_total = 0;

      $transaction = Transaction::whereBetween('transaction_date', [$selected_date_start, $selected_date_end])->where('branch_id', $branch->token)->selectRaw('*, sum(total) as payment_type_total')->groupBy('payment_type')->get();

      foreach($transaction as $value)
      {
        $branch_total += $value->payment_type_total;

        $total += $value->payment_type_total;

        if($value->payment_type == "cash")
        {
          $branch->cash = $value->payment_type_total;

          $cash_total += $value->payment_type_total;
        }
        elseif($value->payment_type == "credit_card")
        {
          $branch->credit_card = $value->payment_type_total;

          $credit_card_total += $value->payment_type_total;
        }
        elseif($value->payment_type == "tng")
        {
          $branch->tng = $value->payment_type_total;

          $tng_total += $value->payment_type_total;
        }
        else
        {
          $branch_other_total += $value->payment_type_total;

          $other_total += $value->payment_type_total;
        }
      }

      if($branch_other_total > 0)
      {
        $branch->other = $branch_other_total;
      }

      $sheet->setCellValue('A'.$started_row, $branch->branch_name);
      $sheet->setCellValue('B'.$started_row, number_format($branch->cash, 2));
      $sheet->setCellValue('C'.$started_row, number_format($branch->credit_card, 2));
      $sheet->setCellValue('D'.$started_row, number_format($branch->tng, 2));
      $sheet->setCellValue('E'.$started_row, number_format($branch->other, 2));
      $sheet->setCellValue('F'.$started_row, number_format($branch->credit_sales, 2));
      $sheet->setCellValue('G'.$started_row, number_format($branch_total, 2));

      $started_row++;
    }

    $started_row++;

    if($cash_total == 0)
    {
      $cash_total = "";
    }
    else
    {
      $cash_total = number_format($cash_total, 2);
    }
    if($credit_card_total == 0)
    {
      $credit_card_total = "";
    }
    else
    {
      $credit_card_total = number_format($credit_card_total);
    }
    if($tng_total == 0)
    {
      $tng_total = "";
    }
    else
    {
      $tng_total = number_format($tng_total, 2);
    }
    if($other_total == 0)
    {
      $other_total = "";
    }
    else
    {
      $other_total = number_format($other_total, 2);
    }
    if($credit_sales_total == 0)
    {
      $credit_sales_total = "";
    }
    else
    {
      $credit_sales_total = number_format($credit_sales_total, 2);
    }
    if($total == 0)
    {
      $total = "";
    }
    else
    {
      $total = number_format($total, 2);
    }

    $sheet->setCellValue('A'.$started_row, "Jumlah:");
    $sheet->setCellValue('B'.$started_row, $cash_total);
    $sheet->setCellValue('C'.$started_row, $credit_card_total);
    $sheet->setCellValue('D'.$started_row, $tng_total);
    $sheet->setCellValue('E'.$started_row, $other_total);
    $sheet->setCellValue('F'.$started_row, $credit_sales_total);
    $sheet->setCellValue('G'.$started_row, $total);

    $sheet->getColumnDimension('A')->setWidth(20);
    $sheet->getColumnDimension('B')->setWidth(11);
    $sheet->getColumnDimension('C')->setWidth(11);
    $sheet->getColumnDimension('D')->setWidth(11);
    $sheet->getColumnDimension('E')->setWidth(11);
    $sheet->getColumnDimension('F')->setWidth(11);
    $sheet->getColumnDimension('G')->setWidth(11);

    $sheet->getStyle('A5:G'.$started_row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    $sheet->getStyle('A5:G5')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('B6:G100')->getAlignment()->setHorizontal('right');

    $writer = new Xlsx($spreadsheet);
    $path = 'storage/report/Sales Report.xlsx';
    $writer->save($path);

    return response()->download($path);
  }

  public function exportBranchReport(Request $request)
  {
    if(!Storage::exists('public/report'))
    {
      Storage::makeDirectory('public/report', 0775, true); //creates directory
    }

    $selected_date_from = date('Y-m-d', strtotime(now()));
    $selected_date_to = date('Y-m-d', strtotime(now()));

    if($request->report_date_from)
    {
      $selected_date_from = $request->report_date_from;
    }

    if($request->report_date_to)
    {
      $selected_date_to = $request->report_date_to;
    }

    $branch_token = $request->branch;

    $selected_date_start = $selected_date_from." 00:00:00";
    $selected_date_end = $selected_date_to." 23:59:59";

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setCellValue('A1', 'Home U (M) Sdn Bhd');
    $sheet->setCellValue('A2', 'Total Sales Report');

    $sheet->mergeCells('A1:G1');
    $sheet->mergeCells('A2:G2');

    $sheet->setCellValue('A3', 'Date:');
    $sheet->setCellValue('B3', date('d-m-Y', strtotime(now())));

    $sheet->setCellValue('B5', 'Cash');
    $sheet->setCellValue('C5', 'Credit Card');
    $sheet->setCellValue('D5', 'T & Go');
    $sheet->setCellValue('E5', 'Other');
    $sheet->setCellValue('F5', 'Credit Sales');
    $sheet->setCellValue('G5', 'Total');

    $sheet->getStyle('A1:G2')->getAlignment()->setHorizontal('center');

    $branch = Branch::where('token', $branch_token)->first();

    $started_row = 4;

    $sheet->setCellValue('A'.$started_row, "Branch : ");
    $sheet->setCellValue('B'.$started_row, $branch->branch_name);

    $branch_cash = 0;
    $branch_credit_card = 0;
    $branch_tng = 0;
    $branch_other = 0;
    $branch_credit_sales = 0;
    $branch_total = 0;

    $cashier_transaction = Transaction::whereBetween('transaction_date', [$selected_date_start, $selected_date_end])->where('branch_id', $branch->token)->groupBy('cashier_name')->get();

    $started_row += 2;

    foreach($cashier_transaction as $cashier)
    {
      $cashier->cash = 0;
      $cashier->credit_card = 0;
      $cashier->tng = 0;
      $cashier->other = 0;
      $cashier->credit_sales = 0;

      $cashier_other_total = 0;
      $cashier_total = 0;

      $transaction = Transaction::whereBetween('transaction_date', [$selected_date_start, $selected_date_end])->where('branch_id', $branch->token)->where('ip', $cashier->ip)->selectRaw('*, sum(total) as payment_type_total')->groupBy('payment_type')->get();

      foreach($transaction as $value)
      {
        $cashier_total += $value->payment_type_total;
        $branch_total += $value->payment_type_total;

        if($value->payment_type == "cash")
        {
          $cashier->cash = $value->payment_type_total;
          $branch_cash += $value->payment_type_total;
        }
        elseif($value->payment_type == "credit_card")
        {
          $cashier->credit_card = $value->payment_type_total;
          $branch_credit_card += $value->payment_type_total;
        }
        elseif($value->payment_type == "tng")
        {
          $cashier->tng = $value->payment_type_total;
          $branch_tng += $value->payment_type_total;
        }
        else
        {
          $cashier_other_total += $value->payment_type_total;
          $branch_other += $value->payment_type_total;
        }
      }

      if($cashier_other_total > 0)
      {
        $cashier->other = $cashier_other_total;
      }

      $sheet->setCellValue('A'.$started_row, $cashier->cashier_name);
      $sheet->setCellValue('B'.$started_row, number_format($cashier->cash, 2));
      $sheet->setCellValue('C'.$started_row, number_format($cashier->credit_card, 2));
      $sheet->setCellValue('D'.$started_row, number_format($cashier->tng, 2));
      $sheet->setCellValue('E'.$started_row, number_format($cashier->other, 2));
      $sheet->setCellValue('F'.$started_row, number_format($cashier->credit_sales, 2));
      $sheet->setCellValue('G'.$started_row, number_format($cashier_total, 2));

      $started_row++;
    }

    $started_row++;

    $sheet->setCellValue('A'.$started_row, "Jumlah: ");
    $sheet->setCellValue('B'.$started_row, number_format($branch_cash, 2));
    $sheet->setCellValue('C'.$started_row, number_format($branch_credit_card, 2));
    $sheet->setCellValue('D'.$started_row, number_format($branch_tng, 2));
    $sheet->setCellValue('E'.$started_row, number_format($branch_other, 2));
    $sheet->setCellValue('F'.$started_row, number_format($branch_credit_sales, 2));
    $sheet->setCellValue('G'.$started_row, number_format($branch_total, 2));

    $sheet->getColumnDimension('A')->setWidth(20);
    $sheet->getColumnDimension('B')->setWidth(11);
    $sheet->getColumnDimension('C')->setWidth(11);
    $sheet->getColumnDimension('D')->setWidth(11);
    $sheet->getColumnDimension('E')->setWidth(11);
    $sheet->getColumnDimension('F')->setWidth(11);
    $sheet->getColumnDimension('G')->setWidth(11);

    $sheet->getStyle('A5:G'.$started_row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    $sheet->getStyle('A5:G5')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('B6:G100')->getAlignment()->setHorizontal('right');

    $writer = new Xlsx($spreadsheet);
    $path = 'storage/report/Branch Report.xlsx';
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
                          ->whereIn('branch_product.branch_id',$request->branch_id)
                          ->select('department.department_name','category.category_name','branch_product.*',DB::raw('SUM(quantity) as total_quantity'))
                          ->groupBy('branch_product.barcode')
                          ->get();

    $branch = Branch::whereIn('id',$request->branch_id)->get();

    $balance_stock = Branch_product::whereIn('branch_id',$request->branch_id)
                                    ->selectRaw('SUM(cost * quantity) as total')
                                    ->get();

    return view('report.stock_balance_report',compact('stock','branch','date','balance_stock'));
  }

  public function exportStockBalance(Request $request)
  {
    $branch_id = explode(",",$request->branch_id[0]);

    if(!Storage::exists('public/report'))
    {
      Storage::makeDirectory('public/report', 0775, true); //creates directory
    }

    $date = date('Y-m-d', strtotime(now()));
    $branch = Branch::whereIn('id',$branch_id)->get();
    $count = count($branch);

    $branch_list = "";
    foreach($branch as $key => $result){
      if($key+1 == $count){
        $branch_list .= $result->branch_name;
      }else{
        $branch_list .= $result->branch_name.',';
      }
    }

    $stock = Branch_product::join('department','department.id','=','branch_product.department_id')
                          ->join('category','category.id','=','branch_product.category_id')
                          ->whereIn('branch_product.branch_id',$branch_id)
                          ->select('department.department_name','category.category_name','branch_product.*',DB::raw('SUM(quantity) as total_quantity'))
                          ->groupBy('branch_product.barcode')
                          ->orderBy('barcode','asc')
                          ->get();

    $stock_array = array();
    foreach($stock as $key => $result){
      array_push($stock_array,[$key+1,$result->department_name,$result->category_name,$result->barcode,$result->product_name,$result->cost,$result->total_quantity,$result->price,$result->cost * $result->total_quantity]);
    }

    $balance_stock = Branch_product::whereIn('branch_id',$branch_id)
                                    ->selectRaw('SUM(cost * quantity) as total')
                                    ->orderBy('barcode','asc')
                                    ->get();

    $branch_stock_quantity = array();
    foreach($branch as $result){

      $a[] = Branch_product::where('branch_id',$result->id)
                            ->select('quantity')
                            ->orderBy('barcode','asc')
                            ->get()
                            ->toArray();
    }

    $row = 5;
    $col = 11;

    $set_col = $col; 
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
    $sheet->setCellValue('B4', $branch_list);
    $sheet->setCellValue('G4', 'Balance Stock');
    $sheet->setCellValue('I4', number_format($balance_stock[0]->total,2));
    foreach($branch as $result){
      $sheet->getCellByColumnAndRow($set_col, $row)->setValue($result->branch_name." Stock Quantity");
      $set_col++;
    }

    //Data
    $sheet->setCellValue('A5', 'Bil');
    $sheet->setCellValue('B5', 'Department');
    $sheet->setCellValue('C5', 'Category');
    $sheet->setCellValue('D5', 'Barcode');
    $sheet->setCellValue('E5', 'Product Name');
    $sheet->setCellValue('F5', 'Cost');
    $sheet->setCellValue('G5', 'Total Quantity');
    $sheet->setCellValue('H5', 'Selling Price');
    $sheet->setCellValue('I5', 'Total Cost');

    foreach($a as $key1 => $result){
      foreach($result as $key2 => $final){
        $sheet->getCellByColumnAndRow($col+$key1, $row+$key2+1)->setValue($final['quantity']); 
      }
    }

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
