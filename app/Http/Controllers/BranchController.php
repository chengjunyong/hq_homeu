<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Branch;
use App\Branch_product;
use App\Product_list;
use App\transaction;
use App\transaction_detail;

class BranchController extends Controller
{
  public function __construct()
  {
      $this->middleware('auth', ['except' => ['branchSync', 'branchSyncCompleted']]);
  }

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

    	// $start = microtime(true);

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

    	// $time = microtime(true) - $start;

    	if($q == true){
    		return "true";
    	}else{
    		return "false";
    	}
    }

    public function branchSync(Request $request)
    {
      $transaction = $request->transaction;
      $transaction = json_decode($transaction, true);
      $transaction_detail = $request->transaction_detail;
      $transaction_detail = json_decode($transaction_detail, true);

      $branch_id = $request->branch_id;
      $session_id = $request->session_id;

      transaction::where('branch_id', $branch_id)->where('session_id', $session_id)->delete();
      transaction_detail::where('branch_id', $branch_id)->where('session_id', $session_id)->delete();

      $transaction_query = [];
      foreach($transaction as $data)
      {
        $query = [
          'branch_transaction_id' => $data['id'],
          'branch_id' => $branch_id,
          'session_id' => $session_id,
          'transaction_no' => $data['transaction_no'],
          'invoice_no' => $data['invoice_no'],
          'user_id' => $data['user_id'],
          'subtotal' => $data['subtotal'],
          'total_discount' => $data['total_discount'],
          'payment' => $data['payment'],
          'payment_type' => $data['payment_type'],
          'payment_type_text' => $data['payment_type_text'],
          'balance' => $data['balance'],
          'total' => $data['total'],
          'void' => $data['void'],
          'completed' => $data['completed'],
          'transaction_date' => $data['transaction_date'],
          'created_at' => $data['created_at'],
          'updated_at' => $data['updated_at']
        ];

        array_push($transaction_query, $query);
      }

      transaction::insert($transaction_query);

      $transaction_detail_query = [];
      foreach($transaction_detail as $data)
      {
        $query = [
          'branch_id' => $branch_id,
          'session_id' => $session_id,
          'branch_transaction_detail_id' => $data['id'],
          'branch_transaction_id' => $data['transaction_id'],
          'product_id' => $data['product_id'],
          'barcode' => $data['barcode'],
          'product_name' => $data['product_name'],
          'quantity' => $data['quantity'],
          'price' => $data['price'],
          'discount' => $data['discount'],
          'subtotal' => $data['subtotal'],
          'total' => $data['total'],
          'void' => $data['void'],
          'created_at' => $data['created_at'],
          'updated_at' => $data['updated_at']
        ];

        array_push($transaction_detail_query, $query);
      }

      transaction_detail::insert($transaction_detail_query);

      // more than 10000, php will return error
      $product_list = product_list::select('department_id', 'category_id', 'barcode', 'product_name', 'price')->where('product_sync', 0)->limit(10000)->get();

      $response = new \stdClass();
      $response->error = 0;
      $response->message = "Transaction sync completed";
      $response->product_list = $product_list->toJson();

      return response()->json($response);
    }

    public function branchSyncCompleted(Request $request)
    {
      $barcode_array = explode("|", $request->barcode_array);

      product_list::where('product_sync', 0)->whereIn('barcode', $barcode_array)->update([
        'product_sync' => 1
      ]);

      $response = new \stdClass();
      $response->error = 0;
      $response->message = "Product list sync completed";

      return response()->json($response);
    }

    public function getSalesReport(Request $request)
    {
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

      return view('sales_report',compact('branch', 'selected_branch', 'selected_date_from', 'selected_date_to', 'transaction'));
    }

    public function getSalesReportDetail($branch_id, $branch_transaction_id)
    {
      $transaction_detail = transaction_detail::where('branch_id', $branch_id)->where('branch_transaction_id', $branch_transaction_id)->paginate(25);

      return view('sales_report_detail',compact('transaction_detail'));
    }
}
