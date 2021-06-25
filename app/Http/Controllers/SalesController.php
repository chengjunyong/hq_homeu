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
    $card_total = 0;
    $tng_total = 0;
    $maybank_qr_total = 0;
    $grab_pay_total = 0;
    $boost_total = 0;
    $other_total = 0;
    $total = 0;

    foreach($branch_list as $branch)
    { 
      $branch->cash = 0;
      $branch->card = 0;
      $branch->tng = 0;
      $branch->maybank_qr = 0;
      $branch->grab_pay = 0;
      $branch->boost = 0;
      $branch->other = 0;

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
        elseif($value->payment_type == "card")
        {
          $branch->card = $value->payment_type_total;

          $card_total += $value->payment_type_total;
        }
        elseif($value->payment_type == "tng")
        {
          $branch->tng = $value->payment_type_total;

          $tng_total += $value->payment_type_total;
        }
        elseif($value->payment_type == "maybank_qr")
        {
          $branch->maybank_qr = $value->payment_type_total;

          $maybank_qr_total += $value->payment_type_total;
        }
        elseif($value->payment_type == "grab_pay")
        {
          $branch->grab_pay = $value->payment_type_total;

          $grab_pay_total += $value->payment_type_total;
        }
        elseif($value->payment_type == "boost")
        {
          $branch->boost = $value->payment_type_total;

          $boost_total += $value->payment_type_total;
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
    $total_summary->card = $card_total;
    $total_summary->tng = $tng_total;
    $total_summary->maybank_qr = $maybank_qr_total;
    $total_summary->grab_pay = $grab_pay_total;
    $total_summary->boost = $boost_total;
    $total_summary->other = $other_total;
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
    $branch_card = 0;
    $branch_tng = 0;
    $branch_maybank_qr = 0;
    $branch_grab_pay = 0;
    $branch_boost = 0;
    $branch_other = 0;
    $branch_total = 0;

    $cashier_transaction = Transaction::whereBetween('transaction_date', [$selected_date_start, $selected_date_end])->where('branch_id', $branch->token)->groupBy('ip')->get();

    foreach($cashier_transaction as $cashier)
    {
      $cashier->cash = 0;
      $cashier->card = 0;
      $cashier->tng = 0;
      $cashier->maybank_qr = 0;
      $cashier->grab_pay = 0;
      $cashier->boost = 0;
      $cashier->other = 0;
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
        elseif($value->payment_type == "card")
        {
          $cashier->card = $value->payment_type_total;
          $branch_card += $value->payment_type_total;
        }
        elseif($value->payment_type == "tng")
        {
          $cashier->tng = $value->payment_type_total;
          $branch_tng += $value->payment_type_total;
        }
        elseif($value->payment_type == "maybank_qr")
        {
          $cashier->maybank_qr = $value->payment_type_total;
          $branch_maybank_qr += $value->payment_type_total;
        }
        elseif($value->payment_type == "grab_pay")
        {
          $cashier->grab_pay = $value->payment_type_total;
          $branch_grab_pay += $value->payment_type_total;
        }
        elseif($value->payment_type == "boost")
        {
          $cashier->boost = $value->payment_type_total;
          $branch_boost += $value->payment_type_total;
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
    $total_summary->card = $branch_card;
    $total_summary->tng = $branch_tng;
    $total_summary->maybank_qr = $branch_maybank_qr;
    $total_summary->grab_pay = $branch_grab_pay;
    $total_summary->boost = $branch_boost;
    $total_summary->other = $branch_other;
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
    $sheet->setCellValue('B3', "Date from : ".$selected_date_from."\nDate to : ".$selected_date_to);

    $sheet->setCellValue('H3', 'Generate Date:');
    $sheet->setCellValue('I3', date('d-m-Y', strtotime(now())));

    $sheet->getStyle("A1:I3")->getAlignment()->setWrapText(true);

    $sheet->setCellValue('B5', 'Cash');
    $sheet->setCellValue('C5', 'Card');
    $sheet->setCellValue('D5', 'T & Go');
    $sheet->setCellValue('E5', 'Maybank QRPay');
    $sheet->setCellValue('F5', 'Grab Pay');
    $sheet->setCellValue('G5', 'Boost');
    $sheet->setCellValue('H5', 'Other');
    $sheet->setCellValue('I5', 'Total');

    $sheet->getStyle('A1:I2')->getAlignment()->setHorizontal('center');

    $branch_list = Branch::get();

    $started_row = 6;

    $cash_total = 0;
    $card_total = 0;
    $tng_total = 0;
    $maybank_qr_total = 0;
    $grab_pay_total = 0;
    $boost_total = 0;
    $other_total = 0;
    $total = 0;

    foreach($branch_list as $branch)
    { 
      $branch->cash = 0;
      $branch->card = 0;
      $branch->tng = 0;
      $branch->maybank_qr = 0;
      $branch->grab_pay = 0;
      $branch->boost = 0;
      $branch->other = 0;

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
        elseif($value->payment_type == "card")
        {
          $branch->card = $value->payment_type_total;

          $card_total += $value->payment_type_total;
        }
        elseif($value->payment_type == "tng")
        {
          $branch->tng = $value->payment_type_total;

          $tng_total += $value->payment_type_total;
        }
        elseif($value->payment_type == "maybank_qr")
        {
          $branch->maybank_qr = $value->payment_type_total;

          $maybank_qr_total += $value->payment_type_total;
        }
        elseif($value->payment_type == "grab_pay")
        {
          $branch->grab_pay = $value->payment_type_total;

          $grab_pay_total += $value->payment_type_total;
        }
        elseif($value->payment_type == "boost")
        {
          $branch->boost = $value->payment_type_total;

          $boost_total += $value->payment_type_total;
        }
        else
        {
          $branch->other += $value->payment_type_total;

          $other_total += $value->payment_type_total;
        }
      }

      $sheet->setCellValue('A'.$started_row, $branch->branch_name);
      $sheet->setCellValue('B'.$started_row, number_format($branch->cash, 2));
      $sheet->setCellValue('C'.$started_row, number_format($branch->card, 2));
      $sheet->setCellValue('D'.$started_row, number_format($branch->tng, 2));
      $sheet->setCellValue('E'.$started_row, number_format($branch->maybank_qr, 2));
      $sheet->setCellValue('F'.$started_row, number_format($branch->grab_pay, 2));
      $sheet->setCellValue('G'.$started_row, number_format($branch->boost, 2));
      $sheet->setCellValue('H'.$started_row, number_format($branch->other, 2));
      $sheet->setCellValue('I'.$started_row, number_format($branch_total, 2));

      $started_row++;
    }

    $started_row++;

    if($cash_total == 0)
      $cash_total = "";
    else
      $cash_total = number_format($cash_total, 2);

    if($card_total == 0)
      $card_total = "";
    else
      $card_total = number_format($card_total);

    if($tng_total == 0)
      $tng_total = "";
    else
      $tng_total = number_format($tng_total, 2);

    if($maybank_qr_total == 0)
      $maybank_qr_total = "";
    else
      $maybank_qr_total = number_format($maybank_qr_total, 2);

    if($grab_pay_total == 0)
      $grab_pay_total = "";
    else
      $grab_pay_total = number_format($grab_pay_total, 2);

    if($boost_total == 0)
      $boost_total = "";
    else
      $boost_total = number_format($boost_total, 2);

    if($other_total == 0)
      $other_total = "";
    else
      $other_total = number_format($other_total, 2);

    if($total == 0)
      $total = "";
    else
      $total = number_format($total, 2);

    $sheet->setCellValue('A'.$started_row, "Jumlah:");
    $sheet->setCellValue('B'.$started_row, $cash_total);
    $sheet->setCellValue('C'.$started_row, $card_total);
    $sheet->setCellValue('D'.$started_row, $tng_total);
    $sheet->setCellValue('E'.$started_row, $maybank_qr_total);
    $sheet->setCellValue('F'.$started_row, $grab_pay_total);
    $sheet->setCellValue('G'.$started_row, $boost_total);
    $sheet->setCellValue('H'.$started_row, $other_total);
    $sheet->setCellValue('I'.$started_row, $total);

    $sheet->getColumnDimension('A')->setWidth(20);
    $sheet->getColumnDimension('B')->setWidth(25);
    $sheet->getColumnDimension('C')->setWidth(11);
    $sheet->getColumnDimension('D')->setWidth(11);
    $sheet->getColumnDimension('E')->setWidth(20);
    $sheet->getColumnDimension('F')->setWidth(11);
    $sheet->getColumnDimension('G')->setWidth(11);
    $sheet->getColumnDimension('H')->setWidth(11);
    $sheet->getColumnDimension('I')->setWidth(11);

    $sheet->getStyle('A5:I'.$started_row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    $sheet->getStyle('A5:I5')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('B6:I100')->getAlignment()->setHorizontal('right');

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
    $sheet->setCellValue('A2', 'Branch Report');


    $sheet->mergeCells('A1:G1');
    $sheet->mergeCells('A2:G2');

    $sheet->setCellValue('A3', 'Date:');
    $sheet->setCellValue('B3', "Date from : ".$selected_date_from."\nDate to : ".$selected_date_to);
    $sheet->getStyle("A1:I3")->getAlignment()->setWrapText(true);

    $sheet->setCellValue('H3', 'Generate Date:');
    $sheet->setCellValue('I3', date('d-m-Y', strtotime(now())));

    $sheet->setCellValue('B5', 'Cash');
    $sheet->setCellValue('C5', 'Credit Card');
    $sheet->setCellValue('D5', 'T & Go');
    $sheet->setCellValue('E5', 'Maybank QRPay');
    $sheet->setCellValue('F5', 'Grab Pay');
    $sheet->setCellValue('G5', 'Boost');
    $sheet->setCellValue('H5', 'Other');
    $sheet->setCellValue('I5', 'Total');

    $sheet->getStyle('A1:I2')->getAlignment()->setHorizontal('center');

    $branch = Branch::where('token', $branch_token)->first();

    $started_row = 4;

    $sheet->setCellValue('A'.$started_row, "Branch : ");
    $sheet->setCellValue('B'.$started_row, $branch->branch_name);

    $branch_cash = 0;
    $branch_card = 0;
    $branch_tng = 0;
    $branch_maybank_qr = 0;
    $branch_grab_pay = 0;
    $branch_boost = 0;
    $branch_other = 0;
    $branch_total = 0;

    $cashier_transaction = Transaction::whereBetween('transaction_date', [$selected_date_start, $selected_date_end])->where('branch_id', $branch->token)->groupBy('cashier_name')->get();

    $started_row += 2;

    foreach($cashier_transaction as $cashier)
    {
      $cashier->cash = 0;
      $cashier->card = 0;
      $cashier->tng = 0;
      $cashier->maybank_qr = 0;
      $cashier->grab_pay = 0;
      $cashier->boost = 0;
      $cashier->other = 0;

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
        elseif($value->payment_type == "card")
        {
          $cashier->card = $value->payment_type_total;
          $branch_card += $value->payment_type_total;
        }
        elseif($value->payment_type == "maybank_qr")
        {
          $cashier->maybank_qr = $value->payment_type_total;
          $branch_maybank_qr += $value->payment_type_total;
        }
        elseif($value->payment_type == "grab_pay")
        {
          $cashier->grab_pay = $value->payment_type_total;
          $branch_grab_pay += $value->payment_type_total;
        }
        elseif($value->payment_type == "boost")
        {
          $cashier->boost = $value->payment_type_total;
          $branch_boost += $value->payment_type_total;
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

      $sheet->setCellValue('A'.$started_row, $cashier->cashier_name);
      $sheet->setCellValue('B'.$started_row, number_format($cashier->cash, 2));
      $sheet->setCellValue('C'.$started_row, number_format($cashier->card, 2));
      $sheet->setCellValue('D'.$started_row, number_format($cashier->tng, 2));
      $sheet->setCellValue('E'.$started_row, number_format($cashier->maybank_qr, 2));
      $sheet->setCellValue('F'.$started_row, number_format($cashier->grab_pay, 2));
      $sheet->setCellValue('G'.$started_row, number_format($cashier->boost, 2));
      $sheet->setCellValue('H'.$started_row, number_format($cashier->other, 2));
      $sheet->setCellValue('I'.$started_row, number_format($cashier_total, 2));

      $started_row++;
    }

    $started_row++;

    $sheet->setCellValue('A'.$started_row, "Jumlah: ");
    $sheet->setCellValue('B'.$started_row, number_format($branch_cash, 2));
    $sheet->setCellValue('C'.$started_row, number_format($branch_card, 2));
    $sheet->setCellValue('D'.$started_row, number_format($branch_tng, 2));
    $sheet->setCellValue('E'.$started_row, number_format($branch_maybank_qr, 2));
    $sheet->setCellValue('F'.$started_row, number_format($branch_grab_pay, 2));
    $sheet->setCellValue('G'.$started_row, number_format($branch_boost, 2));
    $sheet->setCellValue('H'.$started_row, number_format($branch_other, 2));
    $sheet->setCellValue('I'.$started_row, number_format($branch_total, 2));

    $sheet->getColumnDimension('A')->setWidth(20);
    $sheet->getColumnDimension('B')->setWidth(25);
    $sheet->getColumnDimension('C')->setWidth(11);
    $sheet->getColumnDimension('D')->setWidth(11);
    $sheet->getColumnDimension('E')->setWidth(20);
    $sheet->getColumnDimension('F')->setWidth(11);
    $sheet->getColumnDimension('G')->setWidth(11);
    $sheet->getColumnDimension('H')->setWidth(11);
    $sheet->getColumnDimension('I')->setWidth(11);

    $sheet->getStyle('A5:I'.$started_row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    $sheet->getStyle('A5:I5')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('B6:I100')->getAlignment()->setHorizontal('right');

    $writer = new Xlsx($spreadsheet);
    $path = 'storage/report/Branch Report.xlsx';
    $writer->save($path);

    return response()->download($path);
  }

  public function exportBranchStockReport(Request $request)
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

    $branch_token = $request->branch_token;

    $selected_date_start = $selected_date_from." 00:00:00";
    $selected_date_end = $selected_date_to." 23:59:59";

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setCellValue('A1', 'Home U (M) Sdn Bhd');
    $sheet->setCellValue('A2', 'Branch Stock Report');

    $sheet->mergeCells('A1:F1');
    $sheet->mergeCells('A2:F2');

    $sheet->setCellValue('A3', 'Date:');
    $sheet->setCellValue('B3', date('d-m-Y', strtotime(now())));

    $sheet->setCellValue('A5', 'Barcode');
    $sheet->setCellValue('B5', 'Product name');
    $sheet->setCellValue('C5', 'Updated stock');
    $sheet->setCellValue('D5', 'Stock ( different )');
    $sheet->setCellValue('E5', 'Updated by');
    $sheet->setCellValue('F5', 'Updated at');

    $sheet->getStyle('A1:F2')->getAlignment()->setHorizontal('center');

    $branch = Branch::where('token', $branch_token)->first();
    $branch_stock_history = [];
    if($branch)
    {
      $branch_stock_history = Branch_stock_history::where('stock_type', 'branch')->whereBetween('created_at', [$selected_date_start, $selected_date_end])->where('branch_token', $branch->token)->orderBy('created_at')->get();
    }
    
    $started_row = 6;

    foreach($branch_stock_history as $history)
    {
      $sheet->setCellValue('A'.$started_row, $history->barcode);
      $sheet->setCellValue('B'.$started_row, $history->product_name);
      $sheet->setCellValue('C'.$started_row, $history->new_stock_count);
      $sheet->setCellValue('D'.$started_row, $history->difference_count);
      $sheet->setCellValue('E'.$started_row, $history->user_name);
      $sheet->setCellValue('F'.$started_row, $history->created_at);

      $started_row++;
    }

    $sheet->getColumnDimension('A')->setWidth(20);
    $sheet->getColumnDimension('B')->setWidth(21);
    $sheet->getColumnDimension('C')->setWidth(21);
    $sheet->getColumnDimension('D')->setWidth(21);
    $sheet->getColumnDimension('E')->setWidth(21);
    $sheet->getColumnDimension('F')->setWidth(21);

    $sheet->getStyle('A5:F'.($started_row - 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    $sheet->getStyle('A5:F5')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('B6:F'.($started_row - 1))->getAlignment()->setHorizontal('right');

    $writer = new Xlsx($spreadsheet);
    $path = 'storage/report/Branch Stock Report.xlsx';
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

    $files = Storage::allFiles('public/report');

    Storage::delete($files);

    $date = date('d-M-Y h:i:s A', strtotime(now()));
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
      $sheet->getCellByColumnAndRow($set_col, $row)->setValue($result->branch_name);
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

    $time = date('d-M-Y h i s A');
    $time = (string)$time;
    $writer = new Xlsx($spreadsheet);
    $path = 'storage/report/Stock Balance Report ('.$time.').xlsx';
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

  public function getProductSalesReport()
  {
    $url = route('home')."?p=sales_menu";

    $selected_date_from = date('Y-m-d', strtotime(now()));
    $selected_date_to = date('Y-m-d', strtotime(now()));

    $product_list = Product_list::get();

    return view('report.product_sales_report',compact('url','selected_date_from','selected_date_to','product_list'));
  }

  public function postProductSalesReport(Request $request)
  {
    $user = Auth::user();
    $data[] = array();
    $total_quantity = 0;
    $total_sales = 0;
    $total_quantity_day = array();
    $total_sales_day = array();
    $branch_total_quantity = array();
    $branch_total_sales = array(); 

    $from_date = $request->report_date_from;
    $to_date = date('Y-m-d',strtotime($request->report_date_to."+1 day"));

    $diff=strtotime($to_date)-strtotime($from_date);
    $diff_date = $diff / 60 / 60 / 24;

    $branch = Branch::orderBy('token')->get();
    $product_detail = Product_list::where('barcode',$request->barcode)->first();


    foreach($branch as $index => $result){
      $data[$index] = array();
      for($a=0;$a<$diff_date;$a++){
        $tmp_d = date('Y-m-d',strtotime($request->report_date_from."+".$a." day"));
        $tmp = Transaction_detail::selectRaw("SUM(quantity) as quantity,SUM(total) as total,created_at,branch_id")
                                    ->where('barcode',$request->barcode)
                                    ->where('branch_id',$result->token)
                                    ->whereRaw("DATE(created_at) = '$tmp_d'")
                                    ->first();

        array_push($data[$index],$tmp);
      }

      $branch_total_sales[$index] = 0;
      $branch_total_quantity[$index] = 0;

      foreach($data[$index] as $result){
        $total_quantity += intval($result->quantity);
        $total_sales += $result->total;

        $branch_total_sales[$index] += $result->total;
        $branch_total_quantity[$index] += intval($result->quantity);
      }
    }

    //Row Sum
    for($a=0;$a<$diff_date;$a++){
      $total_quantity_day[$a] = 0;
      $total_sales_day[$a] = 0;
      for($b=0;$b<count($data);$b++){
        $total_sales_day[$a] += $data[$b][$a]->total;
        $total_quantity_day[$a] += intval($data[$b][$a]->quantity);
      }
    }

    return view('report.print_product_sales_report',compact('total_quantity','total_sales','branch_total_quantity','branch_total_sales','user','data','from_date','to_date','product_detail','branch','diff_date','total_quantity_day','total_sales_day'));
  }

  public function exportProductSalesReport(Request $request)
  {
    $user = Auth::user();
    //Clear the report folder
    if(!Storage::exists('public/report'))
    {
      Storage::makeDirectory('public/report', 0775, true); //creates directory
    }
    $files = Storage::allFiles('public/report');
    Storage::delete($files);

    //Data Processing
    $data[] = array();
    $total_quantity = 0;
    $total_sales = 0;
    $total_quantity_day = array();
    $total_sales_day = array();
    $branch_total_quantity = array();
    $branch_total_sales = array(); 

    $from_date = $request->report_date_from;
    $to_date = date('Y-m-d',strtotime($request->report_date_to."+1 day"));

    $diff=strtotime($to_date)-strtotime($from_date);
    $diff_date = $diff / 60 / 60 / 24;

    $branch = Branch::orderBy('token')->get();
    $product_detail = Product_list::where('barcode',$request->barcode)->first();


    foreach($branch as $index => $result){
      $data[$index] = array();
      for($a=0;$a<$diff_date;$a++){
        $tmp_d = date('Y-m-d',strtotime($request->report_date_from."+".$a." day"));
        $tmp = Transaction_detail::selectRaw("SUM(quantity) as quantity,SUM(total) as total,created_at,branch_id")
                                    ->where('barcode',$request->barcode)
                                    ->where('branch_id',$result->token)
                                    ->whereRaw("DATE(created_at) = '$tmp_d'")
                                    ->first();

        array_push($data[$index],$tmp);
      }

      $branch_total_sales[$index] = 0;
      $branch_total_quantity[$index] = 0;

      foreach($data[$index] as $result){
        $total_quantity += intval($result->quantity);
        $total_sales += $result->total;

        $branch_total_sales[$index] += $result->total;
        $branch_total_quantity[$index] += intval($result->quantity);
      }
    }

    //Row Sum
    for($a=0;$a<$diff_date;$a++){
      $total_quantity_day[$a] = 0;
      $total_sales_day[$a] = 0;
      for($b=0;$b<count($data);$b++){
        $total_sales_day[$a] += $data[$b][$a]->total;
        $total_quantity_day[$a] += intval($data[$b][$a]->quantity);
      }
    }

    //Start Build Excel File
    $spreadsheet = new Spreadsheet();
    $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(16);
    $spreadsheet->getActiveSheet()->mergeCells('A1:C1');
    $spreadsheet->getActiveSheet()->mergeCells('A2:C2');
    $spreadsheet->getActiveSheet()->mergeCells('A5:C5');
    $sheet = $spreadsheet->getActiveSheet();

    //Header
    $sheet->setCellValue('A1', 'HomeU (M) Sdh Bhd');
    $sheet->setCellValue('A2', 'Product Sales Report');
    $sheet->setCellValue('A4', 'Generate By : '.$user->name);
    $sheet->setCellValue('B4', 'Time : '.now());
    $sheet->setCellValue('A5', 'Product Name : '.$product_detail->product_name);

    $row = 8;
    for($a=0;$a<$diff_date;$a++){
      $sheet->getCellByColumnAndRow(1,$row+$a)->setValue(date('d-M-Y',strtotime($from_date."+".$a." day")));
      $c_row = $row+$a;
    }
    $sheet->getCellByColumnAndRow(1,$c_row+1)->setValue("Total");


    $col = 2;
    foreach($branch as $index => $result){
      $sheet->getCellByColumnAndRow($col+$index,7)->setValue($result->branch_name);
      $c_col = $col+$index;
    }

    $sheet->getStyle('A7:Z7')->getAlignment()->setHorizontal('right');

    $sheet->getCellByColumnAndRow($c_col+1,7)->setValue("Total Quantity");
    $sheet->getCellByColumnAndRow($c_col+2,7)->setValue("Total Sales");
    $sheet->getStyle('A'.($c_row+1).':Z'.($c_row+1))->getFont()->setBold(true);

    $row = 8; $col = 2;
    foreach($data as $c){
      foreach($c as $index => $d){
        $sheet->getCellByColumnAndRow($col,$row+$index)->setValue(($d->quantity)?$d->quantity:0);
      }
      $col++;
    }

    $col = 7;
    foreach($total_quantity_day as $index => $result){
      $sheet->getCellByColumnAndRow($c_col+1,8+$index)->setValue($result);
      $sheet->getCellByColumnAndRow($c_col+2,8+$index)->setValue($total_sales_day[$index]);
      $bottom = 8+$index;
    }

    $sheet->getCellByColumnAndRow($c_col+1,$bottom+1)->setValue($total_quantity);
    $sheet->getCellByColumnAndRow($c_col+2,$bottom+1)->setValue($total_sales);

    $col = 2;
    foreach($branch as $index => $result){
      $sheet->getCellByColumnAndRow($col+$index,$bottom+1)->setValue($branch_total_quantity[$index]);

    }

    $time = date('d-M-Y h i s A');
    $time = (string)$time;
    $writer = new Xlsx($spreadsheet);
    $path = 'storage/report/Product Sales Report ('.$time.').xlsx';
    $writer->save($path);

    return response()->download($path);
  }

  public function exportWarehouseStockReport(Request $request)
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
    $sheet->setCellValue('A2', 'Warehouse Stock Report');

    $sheet->mergeCells('A1:F1');
    $sheet->mergeCells('A2:F2');

    $sheet->setCellValue('A3', 'Date:');
    $sheet->setCellValue('B3', date('d-m-Y', strtotime(now())));

    $sheet->setCellValue('A5', 'Barcode');
    $sheet->setCellValue('B5', 'Product name');
    $sheet->setCellValue('C5', 'Updated stock');
    $sheet->setCellValue('D5', 'Stock ( different )');
    $sheet->setCellValue('E5', 'Updated by');
    $sheet->setCellValue('F5', 'Updated at');

    $sheet->getStyle('A1:F2')->getAlignment()->setHorizontal('center');

    $warehouse_stock_history = Branch_stock_history::where('stock_type', 'warehouse')->whereBetween('created_at', [$selected_date_start, $selected_date_end])->orderBy('created_at')->get();
    
    $started_row = 6;

    foreach($warehouse_stock_history as $history)
    {
      $sheet->setCellValue('A'.$started_row, $history->barcode);
      $sheet->setCellValue('B'.$started_row, $history->product_name);
      $sheet->setCellValue('C'.$started_row, $history->new_stock_count);
      $sheet->setCellValue('D'.$started_row, $history->difference_count);
      $sheet->setCellValue('E'.$started_row, $history->user_name);
      $sheet->setCellValue('F'.$started_row, $history->created_at);

      $started_row++;
    }

    $sheet->getColumnDimension('A')->setWidth(20);
    $sheet->getColumnDimension('B')->setWidth(21);
    $sheet->getColumnDimension('C')->setWidth(21);
    $sheet->getColumnDimension('D')->setWidth(21);
    $sheet->getColumnDimension('E')->setWidth(21);
    $sheet->getColumnDimension('F')->setWidth(21);

    $sheet->getStyle('A5:F'.($started_row - 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    $sheet->getStyle('A5:F5')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('B6:F'.($started_row - 1))->getAlignment()->setHorizontal('right');

    $writer = new Xlsx($spreadsheet);
    $path = 'storage/report/Warehouse Stock Report.xlsx';
    $writer->save($path);

    return response()->download($path);
  }
}
