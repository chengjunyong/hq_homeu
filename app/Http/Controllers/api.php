<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaction;
use App\Transaction_detail;
use App\Product_list;
use App\Branch;

class api extends Controller
{
    public function testresult()
    {
    	return "Good";
    }

    public function branchSync(Request $request)
    {
      $transaction = $request->transaction;
      $transaction_detail = $request->transaction_detail;

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
          'ip' => $data['ip'],
          'cashier_name' => $data['cashier_name'],
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

      $transaction_query = array_chunk($transaction_query,500);
      foreach($transaction_query as $query){
        transaction::insert($query);
      }

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

      $transaction_detail_query = array_chunk($transaction_detail_query,500);
      foreach($transaction_detail_query as $query){
        transaction_detail::insert($query);
      }

      // more than 10000, php will return error
      $product_list = product_list::select('department_id', 'category_id', 'barcode', 'product_name', 'price')->where('product_sync', 0)->get();

      $response = new \stdClass();
      $response->error = 0;
      $response->message = "Transaction sync completed";
      $response->product_list = $product_list;

      return response()->json($response);
    }

    public function branchSyncCompleted(Request $request)
    {
      $barcode_array = $request->barcode_array;

      product_list::where('product_sync', 0)->whereIn('barcode', $barcode_array)->update([
        'product_sync' => 1
      ]);

      $response = new \stdClass();
      $response->error = 0;
      $response->message = "Product list sync completed";

      return response()->json($response);
    }
}
