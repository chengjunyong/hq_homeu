<?php

namespace App\Http\Controllers;

use DateTime;
use App\Brand;
use App\Branch;
use App\Refund;
use App\Do_list;
use App\Category;
use App\Do_detail;
use Carbon\Carbon;
use App\Cash_float;
use App\Department;
use App\SubCategory;
use App\Transaction;
use App\Branch_shift;
use App\Product_list;
use App\Refund_detail;
use App\Branch_product;

use App\Warehouse_stock;
use App\Transaction_detail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Branch_stock_history;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\CelL\DataType;

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

  public function getBranchCashierReport()
  {
    $url = route('home')."?p=sales_menu";

    $selected_date = date('Y-m-d', strtotime(now()));
    
    $branch = Branch::get();

    return view('report.branch_cashier_report',compact('branch', 'selected_date', 'url'));
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
    $cheque_total = 0;
    $boost_total = 0;
    $ebanking_total = 0;
    $other_total = 0;
    $total = 0;

    foreach($branch_list as $branch)
    { 
      $branch->cash = 0;
      $branch->card = 0;
      $branch->tng = 0;
      $branch->maybank_qr = 0;
      $branch->grab_pay = 0;
      $branch->cheque = 0;
      $branch->boost = 0;
      $branch->ebanking = 0;
      $branch->other = 0;
      $branch->total = 0;

      $branch_other_total = 0;
      $branch_total = 0;

      // Need optimize
      $transaction = Transaction::whereBetween('transaction_date', [$selected_date_start, $selected_date_end])->where('branch_id', $branch->token)->selectRaw('*, sum(total) as payment_type_total')->groupBy('payment_type')->get();

      foreach($transaction as $value)
      {
        $branch_total += $value->payment_type_total;

        $total += $value->payment_type_total;

        $branch->total += $value->payment_type_total;

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
        elseif($value->payment_type == "cheque")
        {
          $branch->cheque = $value->payment_type_total;

          $cheque_total += $value->payment_type_total;
        }
        elseif($value->payment_type == "boost")
        {
          $branch->boost = $value->payment_type_total;

          $boost_total += $value->payment_type_total;
        }
        elseif($value->payment_type == "ebanking")
        {
          $branch->ebanking = $value->payment_type_total;

          $ebanking_total += $value->payment_type_total;
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
    $total_summary->cheque = $cheque_total;
    $total_summary->boost = $boost_total;
    $total_summary->ebanking = $ebanking_total;
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
    $branch_cheque = 0;
    $branch_boost = 0;
    $branch_ebanking = 0;
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
      $cashier->cheque = 0;
      $cashier->boost = 0;
      $cashier->ebanking = 0;
      $cashier->other = 0;
      $cashier->total = 0;

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
        elseif($value->payment_type == "cheque")
        {
          $cashier->cheque = $value->payment_type_total;
          $branch_cheque += $value->payment_type_total;
        }
        elseif($value->payment_type == "boost")
        {
          $cashier->boost = $value->payment_type_total;
          $branch_boost += $value->payment_type_total;
        }
        elseif($value->payment_type == "ebanking")
        {
          $cashier->ebanking = $value->payment_type_total;
          $branch_ebanking += $value->payment_type_total;
        }
        else
        {
          $cashier->total += $value->payment_type_total;
          $branch_other += $value->payment_type_total;
        }
      }

      $cashier->total = $cashier_total;
    }

    $total_summary = new \stdClass();
    $total_summary->cash = $branch_cash;
    $total_summary->card = $branch_card;
    $total_summary->tng = $branch_tng;
    $total_summary->maybank_qr = $branch_maybank_qr;
    $total_summary->grab_pay = $branch_grab_pay;
    $total_summary->cheque = $branch_cheque;
    $total_summary->boost = $branch_boost;
    $total_summary->ebanking = $branch_ebanking;
    $total_summary->other = $branch_other;
    $total_summary->total = $branch_total;

    return view('branch_report_detail',compact('cashier_transaction', 'total_summary', 'branch', 'selected_date_from', 'selected_date_to', 'url', 'date', 'user'));
  }

  public function getBranchCashierReportDetail(Request $request)
  {
    $url = route('home')."?p=sales_menu";

    $date = date('Y-m-d H:i:s', strtotime(now()));
    $user = Auth::user();

    $selected_date = date('Y-m-d', strtotime(now()));

    if($request->report_type == "single")
    {
      if($request->report_date){
        $selected_date = $request->report_date;
        $selected_date2 = $request->report_date2;
      }

      $selected_date_from = $selected_date." 00:00:00";
      $selected_date_to = $selected_date2." 23:59:59";

      $branch = Branch::where('token', $request->branch)->first();

      $transaction = Transaction::whereBetween('transaction_date', [$selected_date_from, $selected_date_to])->where('branch_id', $branch->token)->get();

      $payment_type = ['cash', 'card', 'tng', 'maybank_qr', 'grab_pay', 'cheque', 'boost', 'ebanking', 'pandamart', 'grabmart'];
      
      $cashier_list = array();
      $cashier_ip_array = array();

      $branch_shift = Branch_shift::whereBetween('shift_created_at', [$selected_date_from, $selected_date_to])->where('branch_id', $branch->token)->get();
      foreach($branch_shift as $shift)
      {
        if(!in_array($shift->ip, $cashier_ip_array))
        {
          $cashier_detail = new \stdClass();
          $cashier_detail->ip = $shift->ip;

          $cashier_name = $shift->cashier_name;
          if(!$cashier_name)
          {
            $cashier_name = $shift->ip;
          }

          $cashier_detail->cashier_name = $cashier_name;
          $cashier_detail->opening = $shift->opening_amount;
          $cashier_detail->float_in = 0;
          $cashier_detail->cash = 0;
          $cashier_detail->total = 0;
          $cashier_detail->float_out = 0;
          $cashier_detail->refund = 0;
          // $cashier_detail->boss = 0;
          $cashier_detail->remain = 0;

          $payment_type_list = array();
          foreach($payment_type as $type)
          {
            $payment_type_detail = new \stdClass();
            $payment_type_detail->type = $type;
            $payment_type_detail->total = 0;

            array_push($payment_type_list, $payment_type_detail);
          }

          $cashier_detail->payment_type = $payment_type_list;

          array_push($cashier_ip_array, $shift->ip);
          array_push($cashier_list, $cashier_detail);
        }
      }

      $total = 0;
      $total_payment_type = array();
      foreach($payment_type as $type)
      {
        $total_payment_detail = new \stdClass();
        $total_payment_detail->type = $type;
        $total_payment_detail->total = 0;

        array_push($total_payment_type, $total_payment_detail);
      }

      foreach($transaction as $value)
      {
        $total += $value->total;
        if(!in_array($value->ip, $cashier_ip_array))
        {
          $cashier_detail = new \stdClass();
          $cashier_detail->ip = $value->ip;

          $cashier_name = $value->cashier_name;
          if(!$cashier_name)
          {
            $cashier_name = $value->ip;
          }
          $cashier_detail->cashier_name = $cashier_name;
          $cashier_detail->opening = 0; 
          $cashier_detail->float_in = 0;
          $cashier_detail->cash = 0;
          $cashier_detail->total = 0;
          $cashier_detail->float_out = 0;
          $cashier_detail->refund = 0;
          // $cashier_detail->boss = 0;
          $cashier_detail->remain = 0;

          $payment_type_list = array();
          foreach($payment_type as $type)
          {
            $payment_type_detail = new \stdClass();
            $payment_type_detail->type = $type;
            $payment_type_detail->total = 0;

            array_push($payment_type_list, $payment_type_detail);
          }

          $cashier_detail->payment_type = $payment_type_list;

          array_push($cashier_ip_array, $value->ip);
          array_push($cashier_list, $cashier_detail);
        }

        if($value->payment_type == "debit_card" || $value->payment_type == "credit_card")
        {
          $value->payment_type = "card";
        }

        foreach($cashier_list as $cashier)
        {
          if($cashier->ip == $value->ip)
          {
            $cashier->total += $value->total;

            foreach($cashier->payment_type as $cashier_payment_type)
            {
              if($cashier_payment_type->type == $value->payment_type)
              {
                $cashier_payment_type->total += $value->total;
                break;
              }
            }

            if($value->payment_type == "cash")
            {
              $cashier->cash += $value->total;
            }
            break;
          }
        }

        foreach($total_payment_type as $total_payment)
        {
          if($total_payment->type == $value->payment_type)
          {

            $total_payment->total += $value->total;
            break;
          }
        }
      }

      $cash_float = Cash_float::whereBetween('cash_float_created_at', [$selected_date_from, $selected_date_to])->where('branch_id', $branch->token)->get();

      foreach($cash_float as $cash_float_detail)
      {
        foreach($cashier_list as $cashier)
        {
          if($cashier->ip == $cash_float_detail->ip)
          {
            if($cash_float_detail->type == "in")
            {
              $cashier->float_in += $cash_float_detail->amount;
            }
            elseif($cash_float_detail->type == "out")
            {
              $cashier->float_out += $cash_float_detail->amount;
            }
            // elseif($cash_float_detail->type == "boss")
            // {
            //   $cashier->boss += $cash_float_detail->amount;
            // }
            break;
          }
        }
      }

      $refund = Refund::whereBetween('refund_created_at', [$selected_date_from, $selected_date_to])->where('branch_id', $branch->token)->get();

      foreach($refund as $refund_info)
      {
        foreach($cashier_list as $cashier)
        {
          if($cashier->ip == $refund_info->ip)
          {
            $cashier->refund += $refund_info->total;
            break;
          }
        }
      }

      $cashier_total = new \stdClass();
      $cashier_total->opening = 0; 
      $cashier_total->float_in = 0;
      $cashier_total->cash = 0;
      $cashier_total->closing = 0;
      $cashier_total->float_out = 0;
      $cashier_total->refund = 0;
      // $cashier_total->boss = 0;
      $cashier_total->remain = 0;

      foreach($cashier_list as $cashier)
      {
        $remain = $cashier->float_in + $cashier->cash - $cashier->float_out - $cashier->refund;
        $cashier->remain = $remain;

        $cashier_total->opening += $cashier->opening;
        $cashier_total->float_in += $cashier->float_in;
        $cashier_total->cash += $cashier->cash;
        // $cashier_total->total += $cashier->total;
        $cashier_total->closing += $cashier->opening;
        $cashier_total->float_out += $cashier->float_out;
        $cashier_total->refund += $cashier->refund;
        // $cashier_total->boss += $cashier->boss;
        $cashier_total->remain += $cashier->remain;
      }

      return view('report.branch_cashier_report_detail',compact('cashier_list', 'payment_type', 'total_payment_type', 'total', 'cashier_total', 'branch', 'selected_date','selected_date2', 'url', 'date', 'user','transaction'));
    
    }elseif($request->report_type == "all" || $request->report_type == "period"){

      $report_type = $request->report_type;

      if($request->report_date){
        $selected_date = $request->report_date;
        $selected_date2 = $request->report_date2 ?? null;
      }

      if($report_type == "all"){
        $selected_date_from = $selected_date." 00:00:00";
        $selected_date_to = $selected_date." 23:59:59";
      }else{
        $selected_date_from = $selected_date." 00:00:00";
        $selected_date_to = $selected_date2." 23:59:59";
      }

      $branch_list = Branch::get();

      $payment_type = ['cash', 'card', 'tng', 'maybank_qr', 'grab_pay', 'cheque', 'boost', 'ebanking', 'pandamart', 'grabmart'];

      $total = 0;

      $total_payment_type = array();
      foreach($payment_type as $type)
      {
        $total_payment_detail = new \stdClass();
        $total_payment_detail->type = $type;
        $total_payment_detail->total = 0;

        array_push($total_payment_type, $total_payment_detail);
      }

      $branch_total = new \stdClass();
      $branch_total->opening = 0; 
      $branch_total->float_in = 0;
      $branch_total->cash = 0;
      $branch_total->closing = 0;
      $branch_total->float_out = 0;
      $branch_total->refund = 0;
      $branch_total->remain = 0;

      foreach($branch_list as $branch)
      {
        $transaction_list = Transaction::whereBetween('transaction_date', [$selected_date_from, $selected_date_to])->where('branch_id', $branch->token)->get();

        $branch->total = 0;
        foreach($payment_type as $type)
        {
          $branch->$type = 0;
        }

        foreach($transaction_list as $transaction)
        {
          $total += $transaction->total;
          $branch->total += $transaction->total;
          if($transaction->payment_type == "credit_card" || $transaction->payment_type == "debit_card")
          {
            $transaction->payment_type = "card";
          }

          $payment_type_found = 0;
          foreach($payment_type as $type)
          {
            if($transaction->payment_type == $type)
            {
              $payment_type_found = 1;
              break;
            }
          }

          if($payment_type_found == 0)
          {
            $transaction->payment_type = "other";
          }

          foreach($total_payment_type as $total_payment_detail)
          {
            if($total_payment_detail->type == $transaction->payment_type)
            {
              $total_payment_detail->total += $transaction->total;
              break;
            }
          }

          $transaction_payment_type = $transaction->payment_type;
          $branch->$transaction_payment_type += $transaction->total;
        }

        $branch_shift = Branch_shift::whereBetween('shift_created_at', [$selected_date_from, $selected_date_to])->where('branch_id', $branch->token)->groupBy('ip')->get();

        $branch->opening = 0;
        $branch->float_in = 0;
        $branch->float_out = 0;
        $branch->refund = 0;
        $branch->remain = 0;

        foreach($branch_shift as $shift)
        {
          $branch->opening += $shift->opening_amount;
        }

        $total_float_in = Cash_float::whereBetween('cash_float_created_at', [$selected_date_from, $selected_date_to])->where('branch_id', $branch->token)->where('type', 'in')->selectRaw('*, sum(amount) as cash_float_total')->groupBy('type')->first();
        $total_float_out = Cash_float::whereBetween('cash_float_created_at', [$selected_date_from, $selected_date_to])->where('branch_id', $branch->token)->where('type', 'out')->selectRaw('*, sum(amount) as cash_float_total')->groupBy('type')->first();

        if($total_float_in)
        {
          $branch->float_in = $total_float_in->cash_float_total;
        }

        if($total_float_out)
        {
          $branch->float_out = $total_float_out->cash_float_total;
        }

        $refund_list = Refund::whereBetween('refund_created_at', [$selected_date_from, $selected_date_to])->where('branch_id', $branch->token)->selectRaw('*, sum(total) as refund_total')->first();

        if($refund_list)
        {
          $branch->refund = $refund_list->refund_total;
        }

        $branch->remain = $branch->float_in + $branch->cash - $branch->float_out - $branch->refund;

        $branch_total->opening += $branch->opening; 
        $branch_total->float_in += $branch->float_in;
        $branch_total->cash += $branch->cash;
        $branch_total->closing += $branch->opening; 
        $branch_total->float_out += $branch->float_out;
        $branch_total->refund += $branch->refund;
        $branch_total->remain += $branch->remain;
      }

      return view('report.branch_full_report_detail',compact('branch_list', 'payment_type', 'total', 'total_payment_type', 'branch_total', 'selected_date','selected_date2', 'url', 'date', 'user','report_type'));
    
    }
    
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

    $sheet->getStyle("A1:K3")->getAlignment()->setWrapText(true);

    $sheet->setCellValue('B5', 'Cash');
    $sheet->setCellValue('C5', 'Card');
    $sheet->setCellValue('D5', 'T & Go');
    $sheet->setCellValue('E5', 'Maybank QRPay');
    $sheet->setCellValue('F5', 'Grab Pay');
    $sheet->setCellValue('G5', 'Cheque');
    $sheet->setCellValue('H5', 'Boost');
    $sheet->setCellValue('I5', 'E-banking');
    $sheet->setCellValue('J5', 'Other');
    $sheet->setCellValue('K5', 'Total');

    $sheet->getStyle('A1:K2')->getAlignment()->setHorizontal('center');

    $branch_list = Branch::get();

    $started_row = 6;

    $cash_total = 0;
    $card_total = 0;
    $tng_total = 0;
    $maybank_qr_total = 0;
    $grab_pay_total = 0;
    $cheque_total = 0;
    $boost_total = 0;
    $ebanking_total = 0;
    $other_total = 0;
    $total = 0;

    foreach($branch_list as $branch)
    { 
      $branch->cash = 0;
      $branch->card = 0;
      $branch->tng = 0;
      $branch->maybank_qr = 0;
      $branch->grab_pay = 0;
      $branch->cheque = 0;
      $branch->boost = 0;
      $branch->ebanking = 0;
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
        elseif($value->payment_type == "cheque")
        {
          $branch->cheque = $value->payment_type_total;

          $cheque_total += $value->payment_type_total;
        }
        elseif($value->payment_type == "boost")
        {
          $branch->boost = $value->payment_type_total;

          $boost_total += $value->payment_type_total;
        }
        elseif($value->payment_type == "ebanking")
        {
          $branch->ebanking = $value->payment_type_total;

          $ebanking_total += $value->payment_type_total;
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
      $sheet->setCellValue('G'.$started_row, number_format($branch->cheque, 2));
      $sheet->setCellValue('H'.$started_row, number_format($branch->boost, 2));
      $sheet->setCellValue('I'.$started_row, number_format($branch->ebanking, 2));
      $sheet->setCellValue('J'.$started_row, number_format($branch->other, 2));
      $sheet->setCellValue('K'.$started_row, number_format($branch_total, 2));

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
      $card_total = number_format($card_total, 2);

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

    if($cheque_total == 0)
      $cheque_total = "";
    else
      $cheque_total = number_format($cheque_total, 2);

    if($boost_total == 0)
      $boost_total = "";
    else
      $boost_total = number_format($boost_total, 2);

    if($ebanking_total == 0)
      $ebanking_total = "";
    else
      $ebanking_total = number_format($ebanking_total, 2);

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
    $sheet->setCellValue('G'.$started_row, $cheque_total);
    $sheet->setCellValue('H'.$started_row, $boost_total);
    $sheet->setCellValue('I'.$started_row, $ebanking_total);
    $sheet->setCellValue('J'.$started_row, $other_total);
    $sheet->setCellValue('K'.$started_row, $total);

    $sheet->getColumnDimension('A')->setWidth(20);
    $sheet->getColumnDimension('B')->setWidth(25);
    $sheet->getColumnDimension('C')->setWidth(11);
    $sheet->getColumnDimension('D')->setWidth(11);
    $sheet->getColumnDimension('E')->setWidth(20);
    $sheet->getColumnDimension('F')->setWidth(11);
    $sheet->getColumnDimension('G')->setWidth(11);
    $sheet->getColumnDimension('H')->setWidth(11);
    $sheet->getColumnDimension('I')->setWidth(11);
    $sheet->getColumnDimension('J')->setWidth(11);
    $sheet->getColumnDimension('K')->setWidth(11);

    $sheet->getStyle('A5:K'.$started_row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    $sheet->getStyle('A5:K5')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('B6:K100')->getAlignment()->setHorizontal('right');

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
    $sheet->getStyle("A1:K3")->getAlignment()->setWrapText(true);

    $sheet->setCellValue('H3', 'Generate Date:');
    $sheet->setCellValue('I3', date('d-m-Y', strtotime(now())));

    $sheet->setCellValue('B5', 'Cash');
    $sheet->setCellValue('C5', 'Credit Card');
    $sheet->setCellValue('D5', 'T & Go');
    $sheet->setCellValue('E5', 'Maybank QRPay');
    $sheet->setCellValue('F5', 'Grab Pay');
    $sheet->setCellValue('G5', 'Cheque');
    $sheet->setCellValue('H5', 'Boost');
    $sheet->setCellValue('I5', 'E-banking');
    $sheet->setCellValue('J5', 'Other');
    $sheet->setCellValue('K5', 'Total');

    $sheet->getStyle('A1:K2')->getAlignment()->setHorizontal('center');

    $branch = Branch::where('token', $branch_token)->first();

    $started_row = 4;

    $sheet->setCellValue('A'.$started_row, "Branch : ");
    $sheet->setCellValue('B'.$started_row, $branch->branch_name);

    $branch_cash = 0;
    $branch_card = 0;
    $branch_tng = 0;
    $branch_maybank_qr = 0;
    $branch_grab_pay = 0;
    $branch_cheque = 0;
    $branch_boost = 0;
    $branch_ebanking = 0;
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
      $cashier->cheque = 0;
      $cashier->boost = 0;
      $cashier->ebanking = 0;
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
        elseif($value->payment_type == "cheque")
        {
          $cashier->cheque = $value->payment_type_total;
          $branch_cheque += $value->payment_type_total;
        }
        elseif($value->payment_type == "boost")
        {
          $cashier->boost = $value->payment_type_total;
          $branch_boost += $value->payment_type_total;
        }
        elseif($value->payment_type == "ebanking")
        {
          $cashier->ebanking = $value->payment_type_total;
          $branch_ebanking += $value->payment_type_total;
        }
        elseif($value->payment_type == "tng")
        {
          $cashier->tng = $value->payment_type_total;
          $branch_tng += $value->payment_type_total;
        }
        else
        {
          $cashier->other += $value->payment_type_total;
          $branch_other += $value->payment_type_total;
        }
      }

      $sheet->setCellValue('A'.$started_row, $cashier->cashier_name);
      $sheet->setCellValue('B'.$started_row, number_format($cashier->cash, 2));
      $sheet->setCellValue('C'.$started_row, number_format($cashier->card, 2));
      $sheet->setCellValue('D'.$started_row, number_format($cashier->tng, 2));
      $sheet->setCellValue('E'.$started_row, number_format($cashier->maybank_qr, 2));
      $sheet->setCellValue('F'.$started_row, number_format($cashier->grab_pay, 2));
      $sheet->setCellValue('G'.$started_row, number_format($cashier->cheque, 2));
      $sheet->setCellValue('H'.$started_row, number_format($cashier->boost, 2));
      $sheet->setCellValue('I'.$started_row, number_format($cashier->ebanking, 2));
      $sheet->setCellValue('J'.$started_row, number_format($cashier->other, 2));
      $sheet->setCellValue('K'.$started_row, number_format($cashier_total, 2));

      $started_row++;
    }

    $started_row++;

    $sheet->setCellValue('A'.$started_row, "Jumlah: ");
    $sheet->setCellValue('B'.$started_row, number_format($branch_cash, 2));
    $sheet->setCellValue('C'.$started_row, number_format($branch_card, 2));
    $sheet->setCellValue('D'.$started_row, number_format($branch_tng, 2));
    $sheet->setCellValue('E'.$started_row, number_format($branch_maybank_qr, 2));
    $sheet->setCellValue('F'.$started_row, number_format($branch_grab_pay, 2));
    $sheet->setCellValue('G'.$started_row, number_format($branch_cheque, 2));
    $sheet->setCellValue('H'.$started_row, number_format($branch_boost, 2));
    $sheet->setCellValue('I'.$started_row, number_format($branch_ebanking, 2));
    $sheet->setCellValue('J'.$started_row, number_format($branch_other, 2));
    $sheet->setCellValue('K'.$started_row, number_format($branch_total, 2));

    $sheet->getColumnDimension('A')->setWidth(20);
    $sheet->getColumnDimension('B')->setWidth(25);
    $sheet->getColumnDimension('C')->setWidth(11);
    $sheet->getColumnDimension('D')->setWidth(11);
    $sheet->getColumnDimension('E')->setWidth(20);
    $sheet->getColumnDimension('F')->setWidth(11);
    $sheet->getColumnDimension('G')->setWidth(11);
    $sheet->getColumnDimension('H')->setWidth(11);
    $sheet->getColumnDimension('I')->setWidth(11);
    $sheet->getColumnDimension('J')->setWidth(11);
    $sheet->getColumnDimension('K')->setWidth(11);

    $sheet->getStyle('A5:K'.$started_row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    $sheet->getStyle('A5:K5')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('B6:K100')->getAlignment()->setHorizontal('right');

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

    if(end($branch_id) == 'hq' && $count == 0){
      $branch_list .= 'HQ Warehouse';
    }else if(end($branch_id) == 'hq' && $count != 0){
      $branch_list .= ',HQ Warehouse';
    }

    $product_lists = Product_list::query();
    $product_lists =  $product_lists->with('category','department');

    if(in_array(1,$branch_id)){
      $product_lists = $product_lists->with('wb1');
    }

    if(in_array(3,$branch_id)){
      $product_lists = $product_lists->with('wb2');
    }

    if(in_array(4,$branch_id)){
      $product_lists = $product_lists->with('bak');
    }

    if(in_array(5,$branch_id)){
      $product_lists = $product_lists->with('pc');
    }

    if(in_array(6,$branch_id)){
      $product_lists = $product_lists->with('pm1');
    }

    if(in_array(7,$branch_id)){
      $product_lists = $product_lists->with('pm2');
    }

    if(in_array(13,$branch_id)){
      $product_lists = $product_lists->with('g01');
    }

    if(in_array('hq',$branch_id)){
      $product_lists = $product_lists->with('hq');
    }

    $product_lists = $product_lists->get();

    $row = 5;
    $col = 11;

    $set_col = $col; 
    $spreadsheet = new Spreadsheet();
    $spreadsheet->getActiveSheet()->mergeCells('A1:I1');
    $spreadsheet->getActiveSheet()->mergeCells('A2:I2');
    $spreadsheet->getActiveSheet()->mergeCells('G4:H4');
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
    foreach($branch as $result){
      $sheet->getCellByColumnAndRow($set_col, $row)->setValue($result->branch_name);
      $set_col++;
    }

    if(end($branch_id)=='hq'){
      $sheet->getCellByColumnAndRow($set_col, $row)->setValue('HQ Warehouse');
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

    $count=6;
    $total_balance_stock = 0;
    foreach($product_lists as $key => $product){
      $sheet->setCellValue('A'.$count, $key+1);
      $sheet->setCellValue('B'.$count, $product->department->department_name);
      $sheet->setCellValue('C'.$count, $product->category->category_name);
      $sheet->setCellValue('D'.$count, $product->barcode);
      $sheet->setCellValue('E'.$count, $product->product_name);
      $sheet->setCellValue('F'.$count, $product->cost);
      $sheet->setCellValue('G'.$count, "=SUM(K".$count.":W".$count.")");
      $sheet->setCellValue('H'.$count, $product->price);
      $sheet->setCellValue('I'.$count, "=SUM(F".$count."*G".$count.")");
      $set_col = 11;
      foreach($branch_id as $branch){
        switch($branch){
          case 1:
            $sheet->getCellByColumnAndRow($set_col, $count)->setValue($product->wb1->quantity ?? 0);
            break;
          case 3:
            $sheet->getCellByColumnAndRow($set_col, $count)->setValue($product->wb2->quantity ?? 0);
            break;
          case 4:
            $sheet->getCellByColumnAndRow($set_col, $count)->setValue($product->bak->quantity ?? 0);
            break;
          case 5:
            $sheet->getCellByColumnAndRow($set_col, $count)->setValue($product->pc->quantity ?? 0);
            break;
          case 6:
            $sheet->getCellByColumnAndRow($set_col, $count)->setValue($product->pm1->quantity ?? 0);
            break;
          case 7:
            $sheet->getCellByColumnAndRow($set_col, $count)->setValue($product->pm2->quantity ?? 0);
            break;
          case 13:
            $sheet->getCellByColumnAndRow($set_col, $count)->setValue($product->g01->quantity ?? 0);
            break;
          case 'hq':
            $sheet->getCellByColumnAndRow($set_col, $count)->setValue($product->hq->quantity ?? 0);
            break;
        }
        $set_col++;
      }
      $count++;
    }
    $key += 7;
    $sheet->setCellValue('I4', "=SUM(I6:I".$key.")");

    $sheet->getStyle('D')->getAlignment()->setHorizontal('left');
    $sheet->getStyle('A5:A25000')->getAlignment()->setHorizontal('left');
    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(17);
    $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(17);
    $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);
    $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(47);
    $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(10);
    $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(10);
    $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(10);
    $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getStyle('D')->getNumberFormat()->setFormatCode('################################');

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

  public function getProductSalesReport()
  {
    $url = route('home')."?p=sales_menu";

    $selected_date_from = date('Y-m-d', strtotime(now()));
    $selected_date_to = date('Y-m-d', strtotime(now()));

    return view('report.product_sales_report',compact('url','selected_date_from','selected_date_to'));
  }

  public function ajaxGetProduct(Request $request)
  {
    $product = Product_list::where('product_name','LIKE','%'.$request->target.'%')
                            ->orWhere('barcode','LIKE','%'.$request->target.'%')
                            ->get();

    return $product;
  }

  public function postProductSalesReport(Request $request)
  {
    $from = $request->report_date_from;
    $to = $request->report_date_to;

    $date1 = new DateTime($from);
    $date2 = new DateTime($to);
    $interval = $date1->diff($date2);
    $daysDifference = $interval->days + 1;

    $branch = Branch::listing();
    $tokens = $branch->pluck('token')->toArray();
    $product = Product_list::where('product_name',$request->product)->first();

    $details = Transaction_detail::whereBetween('transaction_date',[$from,$to." 23:59:59"])
                                  ->where('barcode',$product->barcode)
                                  ->whereIn('branch_id',$tokens)
                                  ->selectRaw('branch_id, quantity, total, DATE(transaction_date) as tDate')
                                  ->get();
                                  
    $data = [
      'user' => Auth::user()->name,
      'product' => $product,
      'branches' => $branch,
      'from' => $from,
      'to' => $to,
    ];

    return view('report.print_product_sales_report',compact('data','details','daysDifference'));
  }

  public function exportProductSalesReport(Request $request)
  {
    
    //Clear the report folder
    if(!Storage::exists('public/report'))
    {
      Storage::makeDirectory('public/report', 0775, true); //creates directory
    }
    $files = Storage::allFiles('public/report');
    Storage::delete($files);

    //Data Processing
    $user = Auth::user();
    $from = $request->report_date_from;
    $to = $request->report_date_to;

    $date1 = new DateTime($from);
    $date2 = new DateTime($to);
    $interval = $date1->diff($date2);
    $daysDifference = $interval->days + 1;

    $branch = Branch::listing();
    $tokens = $branch->pluck('token')->toArray();
    $product = Product_list::where('product_name',$request->product_export)->first();

    $details = Transaction_detail::whereBetween('transaction_date',[$from,$to." 23:59:59"])
                                  ->where('barcode',$product->barcode)
                                  ->whereIn('branch_id',$tokens)
                                  ->selectRaw('branch_id, quantity, total, DATE(transaction_date) as tDate')
                                  ->get();

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
    $sheet->setCellValue('A5', 'Product Name : '.$product->product_name);

    $row = 8;
    for($a=0;$a<$daysDifference;$a++){
      $sheet->getCellByColumnAndRow(1,$row+$a)->setValue(date('d-M-Y',strtotime($from."+".$a." day")));
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

    $row = 8;
    for($a=0;$a<$daysDifference;$a++){
      $col = 2;
      foreach($branch as $index => $target){
        $result = $details->where('branch_id',$target->token)->where('tDate',date('Y-m-d',strtotime($from."+".$a." day")));
        if($result->count() > 0){
          $sheet->getCellByColumnAndRow($col,$row)->setValue($result->sum('quantity'));
        }else{
          $sheet->getCellByColumnAndRow($col,$row)->setValue(0);
        }
        $col++;
      }
      $sheet->getCellByColumnAndRow($col,$row)->setValue($details->where('tDate',date('Y-m-d',strtotime($from."+".$a." day")))->sum('quantity'));
      $sheet->getCellByColumnAndRow($col+1,$row)->setValue($details->where('tDate',date('Y-m-d',strtotime($from."+".$a." day")))->sum('total'));
      $row++;
    }

    $col = 2;
    foreach($branch as $index => $target){
      $sheet->getCellByColumnAndRow($col,$row)->setValue($details->where('branch_id',$target->token)->sum('quantity'));
      $col++;
    }
    $sheet->getCellByColumnAndRow($col,$row)->setValue($details->sum('quantity'));
    $sheet->getCellByColumnAndRow($col+1,$row)->setValue($details->sum('total'));

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

  public function getDailySalesTransactionReport()
  {
    $url = route('home')."?p=sales_menu";
    $branch = Branch::get();

    return view('daily_sales_transaction_report',compact('url','branch'));
  }

  public function postDailySalesTransactionReport(Request $request)
  {
    $branch = Branch::whereIn('id',$request->branch_id)->get();

    $from_date = $request->report_date_from;
    $to_date = $request->report_date_to;
    $date = Carbon::parse($request->report_date_to);

    $transaction = Transaction::whereIn('branch_id',array_column($branch->toArray(),'token'))
                                ->whereBetween('transaction_date',[$request->report_date_from,$date->addDays(1)])
                                ->orderBy('transaction_no','asc')
                                ->get();
    $user = Auth::user();                            

    return view('report.daily_sales_transaction_report',compact('transaction','from_date','to_date','user'));
  }

  public function transactionCorrection(Request $request)
  {
    if($request->target_date == "" || $request->branch_code == ""){
      return json_encode('Done');
    }else{
      $target_date = date("Ymd",strtotime($request->target_date));
      $prefix = $request->branch_code.$target_date;
      $transaction = Transaction::whereBetween('transaction_date',[$request->target_date,date("Y-m-d",strtotime($request->target_date.'+1 day'))])
                                  ->where('branch_id',$request->token)
                                  ->get();
      $i=5;
      foreach($transaction as $a => $result){
        $number = $a+1;
         while($i>strlen($number)){
            $number = "0".$number;
          }
        $invoice = $prefix.$number;
        Transaction::where('id',$result->id)->update(['transaction_no'=>$invoice]);
      }

      return "Completed Correction";
    }
  }

  public function transactionCorrection2(Request $request)
  {
    if($request->from_date == "" || $request->branch_code == "" || $request->to_date == ""){
      return json_encode('Done');
    }else{
      $from_date = date("Ymd",strtotime($request->from_date));
      $to_date = date("Ymd",strtotime($request->to_date));
      $count = $to_date - $from_date + 1;
      $target_date = date("Ymd",strtotime($request->from_date));

      for($t=0;$t < $count;$t++){
        $prefix = $request->branch_code.$target_date;
        $transaction = Transaction::whereBetween('transaction_date',[$target_date,date("Y-m-d",strtotime($target_date.'+1 day'))])
                                    ->where('branch_id',$request->token)
                                    ->get();
        $i=5;
        foreach($transaction as $a => $result){
          $number = $a+1;
           while($i>strlen($number)){
              $number = "0".$number;
            }
          $invoice = $prefix.$number;
          Transaction::where('id',$result->id)->update(['transaction_no'=>$invoice]);
        }
        $target_date = date("Ymd",strtotime($target_date.'+1 day'));
      }

      return "Completed Correction";
    }
  }

  public function ajaxExportSalesTransactionReport(Request $request)
  {
    $branch = Branch::whereIn('id',$request->branch_id)->get();
    $from_date = $request->start;
    $to_date = $request->end;
    $date = Carbon::parse($request->end);

    $transaction = Transaction::whereIn('branch_id',array_column($branch->toArray(),'token'))
                                ->whereBetween('transaction_date',[$from_date,$date->addDays(1)])
                                ->orderBy('transaction_no','asc')
                                ->get();

    $files = Storage::allFiles('public/report');
    Storage::delete($files);                  
    if(!Storage::exists('public/report'))
    {
      Storage::makeDirectory('public/report', 0775, true); //creates directory
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->mergeCells('A1:F1');
    $sheet->mergeCells('A2:F2');
    $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('A2:F2')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('E')->getAlignment()->setHorizontal('right');
    $sheet->getStyle('F')->getAlignment()->setHorizontal('right');

    $sheet->setCellValue('A1', 'Home U (M) Sdn Bhd');
    $sheet->setCellValue('A2', 'Sales Report');
    $sheet->setCellValue('A4','No');
    $sheet->setCellValue('B4','Invoice No');
    $sheet->setCellValue('C4','Cashier');
    $sheet->setCellValue('D4','Payment Type');
    $sheet->setCellValue('E4','Total');
    $sheet->setCellValue('F4','Reference No');

    $start = 5;
    foreach($transaction as $index => $result){
      $sheet->setCellValue('A'.$start, $index+1);
      $sheet->setCellValue('B'.$start, $result->transaction_no);
      $sheet->setCellValue('C'.$start, $result->cashier_name);
      $sheet->setCellValue('D'.$start, $result->payment_type_text);
      $sheet->setCellValue('E'.$start, $result->total);
      $sheet->setCellValueExplicit('F'.$start, $result->reference_no,DataType::TYPE_STRING2);
      $start++;
    }

    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(15);

    $date = strtotime("now");
    $writer = new Xlsx($spreadsheet);
    $path = 'storage/report/Daily Sales Transaction Report '.$date.'.xlsx';
    $writer->save($path);

    return response()->json($path);
  }

  public function getRefundReport()
  {
    $url = route('home')."?p=sales_menu";

    $branch = Branch::get();

    return view('report.refund_report',compact('url','branch'));
  }

  public function postRefundReport(Request $request)
  {
    $user = Auth::user();
    $target_date = $request->report_date;
    $branch = Branch::where('id',$request->branch_id)->first();

    $refund = Refund::whereBetween('refund_created_at',[$target_date,date('Y-m-d',strtotime($target_date.'+1 days'))])
                      ->where('branch_id',$branch->token)
                      ->orderBy('branch_refund_id','asc')
                      ->get();

    $branch_refund_id = array_column($refund->toArray(),'branch_refund_id');

    $refund_detail = Refund_detail::where('branch_id',$branch->token)
                                    ->whereIn('branch_refund_id',$branch_refund_id)
                                    ->orderBy('branch_refund_id','asc')
                                    ->get();

    return view('print.print_refund',compact('user','target_date','refund','refund_detail','branch'));
  }

  public function ajaxRefundReport(Request $request)
  {
    $branch = Branch::where('id',$request->branch_id)->first();
    $target_date = $request->target_date;

    $refund = Refund::whereBetween('refund_created_at',[$target_date,date('Y-m-d',strtotime($target_date.'+1 days'))])
                      ->where('branch_id',$branch->token)
                      ->orderBy('branch_refund_id','asc')
                      ->get();

    $branch_refund_id = array_column($refund->toArray(),'branch_refund_id');

    $refund_detail = Refund_detail::where('branch_id',$branch->token)
                                    ->whereIn('branch_refund_id',$branch_refund_id)
                                    ->orderBy('branch_refund_id','asc')
                                    ->get();

    $files = Storage::allFiles('public/report');
    Storage::delete($files);                  
    if(!Storage::exists('public/report'))
    {
      Storage::makeDirectory('public/report', 0775, true); //creates directory
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->mergeCells('A1:G1');
    $sheet->mergeCells('A2:G2');
    $sheet->getStyle('A1:E1')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('A2:E2')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('E')->getAlignment()->setHorizontal('right');
    $sheet->getStyle('G')->getAlignment()->setHorizontal('right');
    $sheet->getStyle('F')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('D')->getAlignment()->setHorizontal('left');
    $sheet->getStyle('A')->getAlignment()->setHorizontal('left');
    $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');
    $sheet->setCellValue('A1', 'Home U (M) Sdn Bhd');
    $sheet->setCellValue('A2', 'Refund Report -'.$branch->branch_name);

    $start = 4;
    foreach($refund as $index => $a){
      $sheet->getStyle($start)->getFont()->setBold(true);
      $sheet->setCellValue('A'.$start,'No');
      $sheet->setCellValue('B'.$start,'Refund No');
      $sheet->setCellValue('C'.$start,'Counter Name');
      $sheet->setCellValue('D'.$start,'Cashier Name');
      $sheet->setCellValue('E'.$start,'Refund Date');
      $sheet->mergeCells('E'.$start.':F'.$start);
      $sheet->getStyle('E'.$start)->getAlignment()->setHorizontal('center');
      $start++;
      $sheet->setCellValue('A'.$start,$index+1);
      $sheet->setCellValueExplicit('B'.$start,$a->transaction_no,DataType::TYPE_STRING2);
      $sheet->setCellValue('C'.$start,$a->cashier_name);
      $sheet->setCellValue('D'.$start,$a->created_by);
      $sheet->setCellValue('E'.$start,date("d-M (h:i:s A)",strtotime($a->refund_created_at)));
      $sheet->mergeCells('E'.$start.':F'.$start);
      $sheet->getStyle('E'.$start)->getAlignment()->setHorizontal('center');
      $start++;
      $sheet->getStyle($start)->getFont()->setBold(true);
      $sheet->setCellValue('C'.$start,'Barcode');
      $sheet->setCellValue('D'.$start,'Product');
      $sheet->setCellValue('E'.$start,'Unit Price');
      $sheet->setCellValue('F'.$start,'Quantity');
      $sheet->getStyle('F'.$start)->getAlignment()->setHorizontal('center');
      $sheet->setCellValue('G'.$start,'Sub Total');
      $start++;
      foreach($refund_detail as $index => $b){
        if($a->branch_refund_id == $b->branch_refund_id){
          $sheet->setCellValueExplicit('C'.$start, $b->barcode,DataType::TYPE_STRING2);
          $sheet->setCellValue('D'.$start, $b->product_name);
          $sheet->setCellValue('E'.$start, number_format($b->price,2));
          $sheet->setCellValue('F'.$start, $b->quantity);
          $sheet->setCellValue('G'.$start, number_format($b->total,2));
          $start++;
        }
      }
      $sheet->mergeCells('C'.$start.':F'.$start);
      $sheet->getStyle('C'.$start)->getAlignment()->setHorizontal('center');
      $sheet->getStyle($start)->getFont()->setBold(true);
      $sheet->setCellValue('C'.$start, 'Total');
      $sheet->setCellValue('G'.$start, number_format($a->total,2));
      $start++;$start++;
    }

    $sheet->getStyle($start)->getFont()->setBold(true);
    $sheet->setCellValue('F'.$start, 'Total Quantity Transaction');
    $sheet->setCellValue('G'.$start, count($refund));
    $start++;
    $sheet->getStyle($start)->getFont()->setBold(true);
    $sheet->setCellValue('F'.$start, 'Total Refund Amount');
    $sheet->setCellValue('G'.$start, number_format($refund->sum('total'),2));

    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(15);

    $writer = new Xlsx($spreadsheet);
    $path = 'storage/report/Refund Report.xlsx';
    $writer->save($path);

    return response()->json($path);
  }

  public function getDepartmentAndCategoryReport()
  {
    $url = route('home')."?p=sales_menu";

    $selected_date_from = date('Y-m-d', strtotime(now()));
    $selected_date_to = date('Y-m-d', strtotime(now()));

    $department_list = Department::orderBy('department_name','ASC')->get();
    $category_list = Category::orderBy('category_name','ASC')->get();

    return view('report.department_and_category_report',compact('selected_date_from', 'selected_date_to', 'url', 'department_list', 'category_list'));
  }

  public function exportDepartmentAndCategoryReport(Request $request)
  {
    $branch_list = Branch::whereNotIn('id',[11,12])->get();
    $date_from = $request->export_report_date_from;
    $date_to = $request->export_report_date_to;
    $report_date_from = $date_from." 00:00:00";
    $report_date_to = $date_to." 23:59:59";

    $transaction_details = Transaction_detail::with(['product' => function($q){
                                      $q->with('department','category');
                                    }])
                                    ->where('department_id',$request->export_department_id)
                                    ->whereIn('category_id',$request->export_category_id)
                                    ->whereBetween('transaction_detail_date',[$report_date_from,$report_date_to])
                                    ->get();
    
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setCellValue('A1', 'Home U (M) Sdn Bhd');
    $sheet->setCellValue('A2', 'Department & Category Sales Report');

    $sheet->mergeCells('A1:G1');
    $sheet->mergeCells('A2:G2');

    $sheet->setCellValue('A3', 'Date:');
    $sheet->setCellValue('B3', "Date: ".date('d-m-Y', strtotime($date_from))." until Date: ".date('d-m-Y', strtotime($date_to)));

    $sheet->mergeCells('B3:C3');
    $sheet->setCellValue('I3', 'Branch Sales Quantity');
    $sheet->mergeCells('I3:N3');

    $sheet->setCellValue('E3', 'Generate Date:');
    $sheet->setCellValue('F3', date('d-m-Y', strtotime(now())));
    $sheet->mergeCells('F3:G3');

    $sheet->getStyle("A1:G2")->getAlignment()->setWrapText(true);
    $sheet->getStyle('D')->getNumberFormat()->setFormatCode('################################');

    $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');

    $sheet->setCellValue('A4', 'Bil');
    $sheet->setCellValue('B4', 'Department');
    $sheet->setCellValue('C4', 'Category');
    $sheet->setCellValue('D4', 'Barcode');
    $sheet->setCellValue('E4', 'Product Name');
    $sheet->setCellValue('F4', 'Total Quantity');
    $sheet->setCellValue('G4', 'Total Sales');

    $alphabet = range('A', 'Z');

    $started_alp = 8;
    foreach($branch_list as $branch)
    {
      $col = $alphabet[$started_alp];

      $sheet->setCellValue($col.'4', $branch->branch_name);
      $started_alp++;
    }

    $started_row = 5;
    $index = 0;
    foreach($transaction_details->groupBy('barcode') as $key => $result)
    {
      $sheet->setCellValue('A'.$started_row, ($index + 1));
      $sheet->setCellValue('B'.$started_row, $result->first()->product->department->department_name);
      $sheet->setCellValue('C'.$started_row, $result->first()->product->category->category_name);
      $sheet->setCellValue('D'.$started_row, $result->first()->barcode);
      $sheet->setCellValue('E'.$started_row, $result->first()->product_name);
      $sheet->setCellValue('F'.$started_row, $transaction_details->where('barcode',$result->first()->barcode)->sum('quantity'));
      $sheet->setCellValue('G'.$started_row, $transaction_details->where('barcode',$result->first()->barcode)->sum('total'));

      $started_alp = 8;
      foreach($branch_list as $branch)
      {
        $col = $alphabet[$started_alp];
        $sheet->setCellValue($col.$started_row, $transaction_details->where('branch_id',$branch->token)->where('barcode',$result->first()->barcode)->sum('quantity'));
        
        $started_alp++;
      }

      $started_row++;
      $index++;
    }
    $str = Str::random(5);
    $writer = new Xlsx($spreadsheet);
    $path = 'storage/report/Department and Branch Sales Report '.$str.'.xlsx';
    $writer->save($path);

    $response = new \stdClass();
    $response->error = 0;
    $response->message = "Success.";
    $response->path = $path;

    return response()->json($response);
  }

  public function getMonthlyRefundReport()
  {
    $url = route('home')."?p=sales_menu";

    $branch = Branch::all();

    return view('report.monthly_refund_report',compact('url','branch'));
  }

  public function postMonthlyRefundReport(Request $request)
  {
    $branch = Branch::where('id',$request->branch_id)->first();
    $refund = Refund::where('branch_id',$branch->token)
                      ->whereRaw('MONTH(refund_created_at) ='.date('m',strtotime($request->report_date)))
                      ->get();
    $target_date = $request->report_date;
    $user = Auth::user();

    return view('print.print_monthly_refund',compact('refund','branch','target_date','user'));
  }

  public function ajaxMonthlyRefundReport(Request $request)
  {
    $branch = Branch::where('id',$request->branch_id)->first();
    $refund = Refund::where('branch_id',$branch->token)
                      ->whereRaw('MONTH(refund_created_at) ='.date('m',strtotime($request->target_date)))
                      ->get();
    $target_date = $request->target_date;
    $user = Auth::user();

    $files = Storage::allFiles('public/report');
    Storage::delete($files);                  
    if(!Storage::exists('public/report'))
    {
      Storage::makeDirectory('public/report', 0775, true); //creates directory
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->mergeCells('A1:F1');
    $sheet->mergeCells('A2:F2');
    $sheet->mergeCells('A3:F3');
    $sheet->getStyle('F5')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('A3')->getFont()->setBold(true);
    $sheet->getStyle('E')->getAlignment()->setHorizontal('right');
    $sheet->getStyle('F')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('A')->getAlignment()->setHorizontal('left');
    $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('A2:F2')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('A3:F3')->getAlignment()->setHorizontal('center');
    $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(25);

    $sheet->setCellValue('A1', 'Home U (M) Sdn Bhd');
    $sheet->setCellValue('A2', 'Monthly Refund Report -'.$branch->branch_name);
    $sheet->setCellValue('A3','('.date('F',strtotime($request->target_date)).')');

    $sheet->getStyle(5)->getFont()->setBold(true);
    $sheet->setCellValue('A5','No');
    $sheet->setCellValue('B5','Refund No');
    $sheet->setCellValue('C5','Counter Name');
    $sheet->setCellValue('D5','Cashier Name');
    $sheet->setCellValue('E5','Refund Amount');
    $sheet->setCellValue('F5','Refund Date');
    $sheet->getStyle('E5')->getAlignment()->setHorizontal('center');
    $start = 6;
    foreach($refund as $index => $a){
      $sheet->setCellValue('A'.$start,$index+1);
      $sheet->setCellValue('B'.$start,$a->transaction_no);
      $sheet->setCellValue('C'.$start,$a->cashier_name);
      $sheet->setCellValue('D'.$start,$a->created_by);
      $sheet->setCellValue('E'.$start,number_format($a->total,2));
      $sheet->setCellValue('F'.$start,date('d-M-Y H:i:s A',strtotime($a->refund_created_at)));
      $start++;
    }

    $start++;
    $sheet->getStyle($start)->getFont()->setBold(true);
    $sheet->setCellValue('E'.$start, 'Total Quantity Transaction');
    $sheet->setCellValue('F'.$start, count($refund));
    $start++;
    $sheet->getStyle($start)->getFont()->setBold(true);
    $sheet->setCellValue('E'.$start, 'Total Refund Amount');
    $sheet->setCellValue('F'.$start, number_format($refund->sum('total'),2));

    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(24);

    $writer = new Xlsx($spreadsheet);
    $path = 'storage/report/Monthly Refund Report.xlsx';
    $writer->save($path);

    return response()->json($path);
  }

  public function getDateRangeSalesReport()
  {
    $url = route('home')."?p=sales_menu";

    $branch = Branch::all();

    return view("report.date_range_sales_report",compact('url','branch'));
  }

  public function postDateRangeSalesReport(Request $request)
  {
    $user = Auth::user();
    $from_date = $request->report_date_from;
    $to_date = $request->report_date_to;
    $branch = Branch::find($request->branch_id);
    $date = Carbon::parse($request->report_date_to);

    $transaction = Transaction::where('branch_id',$branch->token)
                                ->whereBetween('transaction_date',[$from_date,$date->addDays(1)])
                                ->get();

    return view('print.print_date_range_sales_report',compact('user','from_date','to_date','transaction'));
  }

  public function ajaxDateRangeSalesReport(Request $request)
  {
    $branch = Branch::where('id',$request->branch_id)->first();
    $from_date = $request->start;
    $to_date = $request->end;
    $date = Carbon::parse($request->end);

    $transaction = Transaction::where('branch_id',$branch->token)
                                ->whereBetween('transaction_date',[$from_date,$date->addDays(1)])
                                ->orderBy('transaction_no','asc')
                                ->get();

    $files = Storage::allFiles('public/report');
    Storage::delete($files);                  
    if(!Storage::exists('public/report'))
    {
      Storage::makeDirectory('public/report', 0775, true); //creates directory
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->mergeCells('A1:H1');
    $sheet->mergeCells('A2:H2');
    $sheet->mergeCells('A3:H3');
    $sheet->getStyle('A1:H1')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('A2:H2')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('A3:H3')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('F')->getAlignment()->setHorizontal('right');
    $sheet->getStyle('E')->getAlignment()->setHorizontal('right');
    $sheet->getStyle('G')->getAlignment()->setHorizontal('right');

    $sheet->setCellValue('A1', 'Home U (M) Sdn Bhd');
    $sheet->setCellValue('A2', 'Date Range Sales Report - '.$branch->branch_name);
    $sheet->setCellValue('A3',$from_date.' to '.$to_date);
    $sheet->setCellValue('A5','No');
    $sheet->setCellValue('B5','Invoice No');
    $sheet->setCellValue('C5','Cashier');
    $sheet->setCellValue('D5','Payment Type');
    $sheet->setCellValue('E5','Round Off');
    $sheet->setCellValue('F5','Total');
    $sheet->setCellValue('G5','Reference No');
    $sheet->setCellValue('H5','Date');

    $start = 6;
    foreach($transaction as $index => $result){
      $sheet->setCellValue('A'.$start, $index+1);
      $sheet->setCellValue('B'.$start, $result->transaction_no);
      $sheet->setCellValue('C'.$start, $result->cashier_name);
      $sheet->setCellValue('D'.$start, $result->payment_type_text);
      $sheet->setCellValue('E'.$start, $result->round_off);
      $sheet->setCellValue('F'.$start, $result->total);
      $sheet->setCellValueExplicit('G'.$start, $result->reference_no,DataType::TYPE_STRING2);
      $sheet->setCellValue('H'.$start, date("d-M-Y h:i:s A",strtotime($result->transaction_date)));
      $start++;
    }

    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(15);
    $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);

    $date = strtotime("now");
    $writer = new Xlsx($spreadsheet);
    $path = 'storage/report/Date Range Sales Report '.$date.'.xlsx';
    $writer->save($path);

    return response()->json($path);
  }

  public function getDeliveryReport()
  {
    $url = route('home')."?p=sales_menu";

    $branch = Branch::all();

    return view("report.delivery_report",compact('url','branch'));
  }

  public function postDeliveryReport(Request $request)
  {
    $user = Auth::user();
    $from_date = $request->report_date_from;
    $to_date = $request->report_date_to;
    $branch = Branch::find($request->branch_id);

    $transaction = Transaction::where('branch_id',$branch->token)
                                ->whereBetween('transaction_date',[$from_date,date('Y-m-d',strtotime($to_date.'+1 days'))])
                                ->where(function($query){
                                  $query->where('payment_type','pandamart');
                                  $query->orWhere('payment_type','grabmart');
                                })
                                ->orderBy('branch_transaction_id','asc')
                                ->get();

    $transaction_id = array_column($transaction->toArray(),'branch_transaction_id');
                            
    $transaction_detail = Transaction_detail::where('branch_id',$branch->token)
                                              ->whereIn('branch_transaction_id',$transaction_id)
                                              ->orderBy('branch_transaction_id','asc')
                                              ->get();    

    return view('print.print_delivery_report',compact('user','branch','transaction','transaction_detail','from_date','to_date'));
  }

  public function ajaxDeliveryReport(Request $request)
  {
    $branch = Branch::where('id',$request->branch_id)->first();
    $from_date = $request->start;
    $to_date = $request->end;

    $transaction = Transaction::whereBetween('transaction_date',[$from_date,date('Y-m-d',strtotime($to_date.'+1 days'))])
                              ->where('branch_id',$branch->token)
                              ->where(function($query){
                                $query->where('payment_type','pandamart');
                                $query->orWhere('payment_type','grabmart');
                              })
                              ->orderBy('branch_transaction_id','asc')
                              ->get();

    $branch_transaction_id = array_column($transaction->toArray(),'branch_transaction_id');

    $transaction_detail = Transaction_detail::where('branch_id',$branch->token)
                                    ->whereIn('branch_transaction_id',$branch_transaction_id)
                                    ->orderBy('branch_transaction_id','asc')
                                    ->get();

    $files = Storage::allFiles('public/report');
    Storage::delete($files);                  
    if(!Storage::exists('public/report'))
    {
      Storage::makeDirectory('public/report', 0775, true); //creates directory
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->mergeCells('A1:G1');
    $sheet->mergeCells('A2:G2');
    $sheet->mergeCells('A3:G3');
    $sheet->getStyle('A1:E1')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('A2:E2')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('A3:E3')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('E')->getAlignment()->setHorizontal('right');
    $sheet->getStyle('G')->getAlignment()->setHorizontal('right');
    $sheet->getStyle('F')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('D')->getAlignment()->setHorizontal('left');
    $sheet->getStyle('A')->getAlignment()->setHorizontal('left');
    $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal('center');
    $sheet->setCellValue('A1', 'Home U (M) Sdn Bhd');
    $sheet->setCellValue('A2', 'PandaMart & GrabMart Sales Report -'.$branch->branch_name);
    $sheet->setCellValue('A3', date('d-M-Y',strtotime($from_date)).' to '.date('d-M-Y',strtotime($to_date)));

    $start = 5;
    foreach($transaction as $index => $a){
      $sheet->getStyle($start)->getFont()->setBold(true);
      $sheet->setCellValue('A'.$start,'No');
      $sheet->setCellValue('B'.$start,'Transaction No');
      $sheet->setCellValue('C'.$start,'Counter Name');
      $sheet->setCellValue('D'.$start,'Cashier Name');
      $sheet->setCellValue('E'.$start,'Refund Date');
      $sheet->mergeCells('E'.$start.':F'.$start);
      $sheet->getStyle('E'.$start)->getAlignment()->setHorizontal('center');
      $start++;
      $sheet->setCellValue('A'.$start,$index+1);
      $sheet->setCellValueExplicit('B'.$start,$a->transaction_no,DataType::TYPE_STRING2);
      $sheet->setCellValue('C'.$start,$a->cashier_name);
      $sheet->setCellValue('D'.$start,$a->created_by);
      $sheet->setCellValue('E'.$start,date("d-M (h:i:s A)",strtotime($a->transaction_date)));
      $sheet->mergeCells('E'.$start.':F'.$start);
      $sheet->getStyle('E'.$start)->getAlignment()->setHorizontal('center');
      $start++;
      $sheet->getStyle($start)->getFont()->setBold(true);
      $sheet->setCellValue('C'.$start,'Barcode');
      $sheet->setCellValue('D'.$start,'Product');
      $sheet->setCellValue('E'.$start,'Unit Price');
      $sheet->setCellValue('F'.$start,'Quantity');
      $sheet->getStyle('F'.$start)->getAlignment()->setHorizontal('center');
      $sheet->setCellValue('G'.$start,'Sub Total');
      $start++;
      foreach($transaction_detail as $index => $b){
        if($a->branch_transaction_id == $b->branch_transaction_id){
          $sheet->setCellValueExplicit('C'.$start, $b->barcode,DataType::TYPE_STRING2);
          $sheet->setCellValue('D'.$start, $b->product_name);
          $sheet->setCellValue('E'.$start, number_format($b->price,2));
          $sheet->setCellValue('F'.$start, $b->quantity);
          $sheet->setCellValue('G'.$start, number_format($b->total,2));
          $start++;
        }
      }
      $sheet->mergeCells('C'.$start.':F'.$start);
      $sheet->getStyle('C'.$start)->getAlignment()->setHorizontal('center');
      $sheet->getStyle($start)->getFont()->setBold(true);
      $sheet->setCellValue('C'.$start, 'Total');
      $sheet->setCellValue('G'.$start, number_format($a->total,2));
      $start++;$start++;
    }

    $sheet->getStyle($start)->getFont()->setBold(true);
    $sheet->setCellValue('F'.$start, 'Total Quantity Transaction');
    $sheet->setCellValue('G'.$start, count($transaction));
    $start++;
    $sheet->getStyle($start)->getFont()->setBold(true);
    $sheet->setCellValue('F'.$start, 'Total Sales');
    $sheet->setCellValue('G'.$start, number_format($transaction->sum('total'),2));

    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(15);

    $writer = new Xlsx($spreadsheet);
    $path = 'storage/report/PandaMart & GrabMart Sales Report.xlsx';
    $writer->save($path);

    return response()->json($path);
  }

  public function getStockBalanceBranchReport()
  {
    $url = route('home')."?p=sales_menu";
    $branches = Branch::orderBy('id','ASC')->get();

    return view('report.stock_balance_branch_index',compact('branches','url'));
  }

  public function postStockBalanceBranchReport(Request $request)
  {
    $data = collect();
    $branches = Branch::whereIn('id',$request->branch_id)->orderBy('id','ASC')->get();
    $stocks = Branch_product::whereIn('branch_id',$request->branch_id)
                            ->where('department_id',21)
                            ->where('deleted_at',NULL)
                            ->orderBy('branch_id','ASC')
                            ->orderBy('barcode','ASC')
                            ->get();
    
    $warehouse = array_filter($request->branch_id,function($a){return $a == 99;});

    if($warehouse != null){
      $warehouse = Warehouse_stock::where('department_id',21)
                                  ->where('deleted_at',NULL)
                                  ->orderBy('barcode','ASC')
                                  ->get();
    }

    $items = Product_list::join('category as c','c.id','=','product_list.category_id')
                          ->where('product_list.department_id',21)
                          ->where('deleted_at',NULL)
                          ->orderBy('barcode','ASC')
                          ->get();

    foreach($items as $index => $item){
      $tmp = array();
      foreach($branches as $index => $branch){
        switch($branch->id){
          case 1:
            $tmp['wb1'] = floatval($stocks->filter(function($data) use ($item){return $data->barcode === $item->barcode && $data->branch_id == 1;})->first()->quantity ?? 0);
            break;
          case 3:
            $tmp['wb2'] = floatval($stocks->filter(function($data) use ($item){return $data->barcode === $item->barcode && $data->branch_id == 3;})->first()->quantity ?? 0);
            break;
          case 4:
            $tmp['bac'] = floatval($stocks->filter(function($data) use ($item){return $data->barcode === $item->barcode && $data->branch_id == 4;})->first()->quantity ?? 0);
            break;
          case 5:
            $tmp['pc'] = floatval($stocks->filter(function($data) use ($item){return $data->barcode === $item->barcode && $data->branch_id == 5;})->first()->quantity ?? 0);
            break;
          case 6:
            $tmp['pm1'] = floatval($stocks->filter(function($data) use ($item){return $data->barcode === $item->barcode && $data->branch_id == 6;})->first()->quantity ?? 0);
            break;
          case 7:
            $tmp['pm2'] = floatval($stocks->filter(function($data) use ($item){return $data->barcode === $item->barcode && $data->branch_id == 7;})->first()->quantity ?? 0);
            break;
          case 13:
            $tmp['gerong'] = floatval($stocks->filter(function($data) use ($item){return $data->barcode === $item->barcode && $data->branch_id == 13;})->first()->quantity ?? 0);
            break;
        }
      }

      if($warehouse != null){
        $tmp['hq'] = floatval($warehouse->filter(function($data) use ($item){return $data->barcode === $item->barcode;})->first()->quantity ?? 0);
      }

      $data->push([
        'barcode' => $item->barcode,
        'product_name' => $item->product_name,
        'category' => $item->category_name,
        'branch_qty' => $tmp,
      ]);
    }

    return view('report.stock_balance_branch_report',compact('data','branches','warehouse'));
  }

  public function ajaxStockBalanceBranchReport(Request $request)
  {
    $data = collect();
    $branches = Branch::whereIn('id',$request->branch_id)->orderBy('id','ASC')->get();
    $stocks = Branch_product::whereIn('branch_id',$request->branch_id)
                            ->where('department_id',21)
                            ->where('deleted_at',NULL)
                            ->orderBy('branch_id','ASC')
                            ->orderBy('barcode','ASC')
                            ->get();
    
    $warehouse = array_filter($request->branch_id,function($a){return $a == 99;});

    if($warehouse != null){
      $warehouse = Warehouse_stock::where('department_id',21)
                                  ->where('deleted_at',NULL)
                                  ->orderBy('barcode','ASC')
                                  ->get();
    }

    $items = Product_list::join('category as c','c.id','=','product_list.category_id')
                          ->where('product_list.department_id',21)
                          ->where('deleted_at',NULL)
                          ->orderBy('barcode','ASC')
                          ->get();

    foreach($items as $index => $item){
      $tmp = array();
      foreach($branches as $index => $branch){
        switch($branch->id){
          case 1:
            $tmp['wb1'] = floatval($stocks->filter(function($data) use ($item){return $data->barcode === $item->barcode && $data->branch_id == 1;})->first()->quantity ?? 0);
            break;
          case 3:
            $tmp['wb2'] = floatval($stocks->filter(function($data) use ($item){return $data->barcode === $item->barcode && $data->branch_id == 3;})->first()->quantity ?? 0);
            break;
          case 4:
            $tmp['bac'] = floatval($stocks->filter(function($data) use ($item){return $data->barcode === $item->barcode && $data->branch_id == 4;})->first()->quantity ?? 0);
            break;
          case 5:
            $tmp['pc'] = floatval($stocks->filter(function($data) use ($item){return $data->barcode === $item->barcode && $data->branch_id == 5;})->first()->quantity ?? 0);
            break;
          case 6:
            $tmp['pm1'] = floatval($stocks->filter(function($data) use ($item){return $data->barcode === $item->barcode && $data->branch_id == 6;})->first()->quantity ?? 0);
            break;
          case 7:
            $tmp['pm2'] = floatval($stocks->filter(function($data) use ($item){return $data->barcode === $item->barcode && $data->branch_id == 7;})->first()->quantity ?? 0);
            break;
          case 13:
            $tmp['gerong'] = floatval($stocks->filter(function($data) use ($item){return $data->barcode === $item->barcode && $data->branch_id == 13;})->first()->quantity ?? 0);
            break;
        }
      }

      if($warehouse != null){
        $tmp['hq'] = floatval($warehouse->filter(function($data) use ($item){return $data->barcode === $item->barcode;})->first()->quantity ?? 0);
      }

      $data->push([
        'barcode' => $item->barcode,
        'product_name' => $item->product_name,
        'category' => $item->category_name,
        'branch_qty' => $tmp,
      ]);
    }

    $files = Storage::allFiles('public/report');
    Storage::delete($files);                  
    if(!Storage::exists('public/report'))
    {
      Storage::makeDirectory('public/report', 0775, true); //creates directory
    }

    $col = ['E','F','G','H','I','J','K','L'];
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->mergeCells('A1:A2');
    $sheet->mergeCells('B1:B2');
    $sheet->mergeCells('C1:C2');
    $sheet->mergeCells('D1:D2');
    $sheet->setCellValue('A1', 'Bil');
    $sheet->setCellValue('B1', 'Category');
    $sheet->setCellValue('C1', 'Barcode');
    $sheet->setCellValue('D1', 'Product Name');
    $sheet->setCellValue('E1', 'Stock Balance');

    $k = 0;
    foreach($branches as $index => $branch){
      $k++;
      $sheet->setCellValue($col[$index].'2', $branch->branch_name);
    }


    $k = $k - 1;

    $sheet->mergeCells('E1:'.$col[$k].'1');
    $sheet->getStyle('E1')->getAlignment()->setHorizontal('center');


    //start content
    $start = 3;
    $index = 1;
    foreach($data->sortBy('category') as $result){
      $sheet->setCellValue('A'.$start, $index);
      $sheet->setCellValue('B'.$start, $result['category']);
      $sheet->setCellValue('C'.$start, $result['barcode']);
      $sheet->setCellValue('D'.$start, $result['product_name']);
      $a=0;
      foreach($result['branch_qty'] as $branch_qty){
        $sheet->setCellValue($col[$a].$start, $branch_qty);
        $a++;
      }
      $index++;
      $start++;
    }
    //end content


    $writer = new Xlsx($spreadsheet);
    $path = 'storage/report/Stock Kawalan Report -'.Str::random(8).'.xlsx';
    $writer->save($path);

    return response()->json($path);
  }

  public function getStockBalanceHistory()
  {
    $url = route('home')."?p=sales_menu";

    $files = collect();

    foreach(Storage::allFiles('public/monthly-report') as $file){
      $publicUrl = Storage::url($file);
      $createdDate = Storage::lastModified($file);

      $files[] = [
        'name' => pathinfo($file, PATHINFO_FILENAME),
        'path' => $publicUrl,
        'created_at' => $createdDate,
      ];
    }

    return view('report.stock_balance_history',compact('url','files'));

  }

  public function getStockInReport()
  {
    $url = route('home')."?p=sales_menu";
    $departments = Department::with('categories')->get();
    $branches = Branch::listing();
    $brands = Brand::orderBy('name','ASC')->get();

    return view('report.stock_in_report',compact('url','departments','branches','brands'));
  }

  public function postStockInReport(Request $request)
  {
    $data = Do_detail::with('do_list','product')
                      ->whereHas('do_list', function($q) use ($request){
                        if($request->from_branch != 'all'){
                          $q->where('from_branch_id',$request->from_branch);
                        }
                        $q->where('to_branch_id',$request->to_branch);
                        $q->whereBetween('completed_time',[$request->report_date_from.' 00:00:00',$request->report_date_to.' 23:59:59']);
                      })
                      ->when($request->product_name != null, function($q) use ($request){
                        $q->where('product_name','LIKE','%'.$request->product_name.'%');
                      })
                      ->when($request->barcode != null, function($q) use ($request){
                        $q->where('barcode','LIKE','%'.$request->barcode.'%');
                      })
                      ->when($request->department_id != '', function($q) use ($request){
                        $q->whereHas('product',function($x) use ($request){
                          $x->where('department_id',$request->department_id);
                          if(isset($request->category_id) && $request->category_id != null){
                            $x->whereIn('category_id',$request->category_id);
                          }
                          if(isset($request->brand_id) && $request->brand_id != null){
                            $x->where('brand_id','=',$request->brand_id);
                          }
                        });
                      })
                      ->groupBy('barcode')
                      ->select('*',DB::raw('SUM(quantity) as total_quantity'))
                      ->get();

    $toBranch = $request->to_branch != 0 ? Branch::find($request->to_branch)->branch_name : 'HQ';
    if($request->from_branch == 'all'){
      $fromBranch = 'All Branches';
    }else if($request->from_branch == '0'){
      $fromBranch = 'HQ';
    }else{
      $fromBranch = Branch::find($request->from_branch)->branch_name;
    }

    $response = [
      'from_date' => $request->report_date_from,
      'to_date' => $request->report_date_to,
      'user' => Auth::user(),
      'from_branch' => $fromBranch,
      'to_branch' => $toBranch,
    ];

    return view('report.stock_in_view',compact('data','response'));
  }

  public function getStockOutReport()
  {
    $url = route('home')."?p=sales_menu";
    $departments = Department::with('categories')->orderBy('department_name','ASC')->get();
    $branches = Branch::listing();
    $brands = Brand::orderBy('name','ASC')->get();

    return view('report.stock_out_report',compact('url','departments','branches','brands'));
  }

  public function postStockOutReport(Request $request)
  {
    $data = Do_detail::with('do_list','product')
                      ->whereHas('do_list', function($q) use ($request){
                        if($request->to_branch != 'all'){
                          $q->where('to_branch_id',$request->to_branch);
                        }
                        $q->where('from_branch_id',$request->from_branch);
                        $q->whereBetween('completed_time',[$request->report_date_from.' 00:00:00',$request->report_date_to.' 23:59:59']);
                      })
                      ->when($request->product_name != 'null', function($q) use ($request){
                        $q->where('product_name','LIKE','%'.$request->product_name.'%');
                      })
                      ->when($request->barcode != null, function($q) use ($request){
                        $q->where('barcode','LIKE','%'.$request->barcode.'%');
                      })
                      ->when($request->department_id != '', function($q) use ($request){
                        $q->whereHas('product',function($x) use ($request){
                          $x->where('department_id',$request->department_id);
                          if(isset($request->category_id) && $request->category_id != null){
                            $x->whereIn('category_id',$request->category_id);
                          }
                          if(isset($request->brand_id) && $request->brand_id != null){
                            $x->where('brand_id','=',$request->brand_id);
                          }    
                        });
                      })
                      ->groupBy('barcode')
                      ->select('*',DB::raw('SUM(quantity) as total_quantity'))
                      ->get();

    $fromBranch = $request->to_branch != 0 ? Branch::find($request->to_branch)->branch_name : 'HQ';
    if($request->to_branch == 'all'){
      $toBranch = 'All Branches';
    }else if($request->to_branch == '0'){
      $toBranch = 'HQ';
    }else{
      $toBranch = Branch::find($request->to_branch)->branch_name;
    }

    $response = [
      'from_date' => $request->report_date_from,
      'to_date' => $request->report_date_to,
      'user' => Auth::user(),
      'from_branch' => $fromBranch,
      'to_branch' => $toBranch,
    ];

    return view('report.stock_out_view',compact('data','response'));
  }

  public function getItemBasedSalesReport(Request $request)
  {
    $url = route('home')."?p=sales_menu";
    $branches = Branch::listing();
    $departments = Department::orderBy('department_name','ASC')->get();
    $brands = Brand::orderBy('name','ASC')->get();
    $subCategories = SubCategory::orderBy('name','ASC')->get();
    $categories = Category::orderBy('category_name','ASC')->get();

    return view('report.item_based_report',compact('branches','url','departments','brands','subCategories','categories'));
  }

  public function printItemBasedSalesReport(Request $request)
  {
    $from = $request->start;
    $to = $request->end;
    $branches = Branch::whereIn('id',$request->branches)->get();

    $details = Transaction_detail::with('product')
                                  ->whereHas('product',function($q) use ($request){
                                    if(isset($request->department) && $request->department != null){
                                      $q->where('department_id',$request->department);
                                    }

                                    if(isset($request->sub_category_ids) && $request->sub_category_ids != null){
                                      $q->whereIn('sub_category_id',$request->sub_category_ids);
                                    }

                                    if(isset($request->brand_ids) && $request->brand_ids != null){
                                      $q->whereIn('brand_id',$request->brand_ids);
                                    }

                                    if(isset($request->category_ids) && $request->category_ids != null){
                                      $q->whereIn('category_id',$request->category_ids);
                                    }
                                  })
                                  ->whereBetween('transaction_date',[$request->start,$request->end." 23:59:59"])
                                  ->whereIn('branch_id',$branches->pluck('token')->toArray())
                                  ->select('*',DB::raw('SUM(quantity) as total_quantity'))
                                  ->orderBy('total_quantity','DESC')
                                  ->groupBy('barcode')
                                  ->paginate(1000);

    return view('report.print_item_based_report',compact('details','branches','from','to'));
  }

  public function getBranchRestockReport()
  {

  }

  public function printBranchRestockReport(Request $request)
  {

  }

}
