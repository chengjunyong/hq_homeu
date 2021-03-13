<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Branch;
use App\transaction;
use App\transaction_detail;

class SalesController extends Controller
{
  public function getSalesReport(Request $request)
  {
    $url = route('home')."?p=sales_menu";
    $branch = Branch::get();

    $selected_branch = null;
    $selected_branch_token = null;
    $selected_date_from = date('Y-m-d', strtotime(now()));
    $selected_date_to = date('Y-m-d', strtotime(now()));

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
      $transaction = transaction::whereBetween('transaction_date', [$selected_date_start, $selected_date_end])->where('branch_id', $selected_branch->token)->paginate(25);
    }
    else
    {
      $transaction = transaction::whereBetween('transaction_date', [$selected_date_start, $selected_date_end])->where('branch_id', null)->paginate(25);
    }

    return view('sales_report',compact('branch', 'selected_branch', 'selected_date_from', 'selected_date_to', 'transaction','url'));
  }

  public function getSalesReportDetail($branch_id, $branch_transaction_id)
  { 
    $url = route('home')."?p=product_menu";
    $transaction_detail = transaction_detail::where('branch_id', $branch_id)->where('branch_transaction_id', $branch_transaction_id)->paginate(25);

    return view('sales_report_detail',compact('transaction_detail','url'));
  }
}
