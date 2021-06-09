<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaction;
use App\Transaction_detail;
use App\Branch_product;
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

      $previous_transaction_detail = transaction_detail::where('branch_id', $branch_id)->where('session_id', $session_id)->selectRaw('*, sum(quantity) as total_quantity')->groupBy('product_id')->get();

      transaction::where('branch_id', $branch_id)->where('session_id', $session_id)->delete();
      transaction_detail::where('branch_id', $branch_id)->where('session_id', $session_id)->delete();
      $branch_detail = Branch::where('token', $branch_id)->first();

      if(!$branch_detail)
      {
        $response = new \stdClass();
        $response->error = 1;
        $response->message = "Branch ID ".$branch_id." not found.";

        return response()->json($response);
      }

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
          'reference_no' => $data['reference_no'],
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

      $transaction_product = array();

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

        $product_name = "product_".$data['product_id'];
        if(!isset($transaction_product[$product_name]))
        {
          $transaction_product[$product_name] = new \stdClass();
          $transaction_product[$product_name]->barcode = $data['barcode'];
          $transaction_product[$product_name]->quantity = 0;
        }

        $transaction_product[$product_name]->quantity += $data['quantity'];

        array_push($transaction_detail_query, $query);
      }

      foreach($transaction_product as $key => $transaction_product_detail)
      {
        $product_id = str_replace("product_", "", $key);
        foreach($previous_transaction_detail as $previous_transaction)
        {
          if($product_id == $previous_transaction->product_id)
          {
            $transaction_product[$key]->quantity = $transaction_product_detail->quantity - $previous_transaction->total_quantity;
            break;
          }
        }
      } 

      $transaction_detail_query = array_chunk($transaction_detail_query,500);
      foreach($transaction_detail_query as $query){
        transaction_detail::insert($query);
      }

      foreach($transaction_product as $transaction_product_detail)
      {
        $branch_product = Branch_product::where('branch_id', $branch_detail->id)->where('barcode', $transaction_product_detail->barcode)->first();

        if($branch_product)
        {
          $stock = $branch_product->quantity - $transaction_product_detail->quantity;
          Branch_product::where('id', $branch_product->id)->update([
            'quantity' => $stock
          ]);
        }
      }

      // more than 10000, php will return error
      $product_list = Branch_product::select('department_id', 'category_id', 'barcode', 'product_name', 'price')->where('branch_id', $branch_detail->id)->where('product_sync', 0)->get();

      $response = new \stdClass();
      $response->error = 0;
      $response->message = "Transaction sync completed";
      $response->product_list = $product_list;

      return response()->json($response);
    }

    public function branchSyncCompleted(Request $request)
    {
      $barcode_array = $request->barcode_array;
      $branch_id = $request->branch_id;

      $branch_detail = Branch::where('token', $branch_id)->first();

      Branch_product::where('product_sync', 0)->where('branch_id', $branch_detail->id)->whereIn('barcode', $barcode_array)->update([
        'product_sync' => 1
      ]);

      $response = new \stdClass();
      $response->error = 0;
      $response->message = "Product list sync completed";

      return response()->json($response);
    }

    public function syncBranchProductList(Request $request)
    {
      $branch_id = $request->branch_id;
      $branch_detail = Branch::where('token', $branch_id)->first();

      if(!$branch_detail)
      {
        $response = new \stdClass();
        $response->error = 1;
        $response->message = "Branch ID ".$branch_id." not found.";

        return response()->json($response);
      }
      
      $product_list = Branch_product::select('department_id', 'category_id', 'barcode', 'product_name', 'price')->where('branch_id', $branch_detail->id)->where('product_sync', 0)->get();

      $response = new \stdClass();
      $response->error = 0;
      $response->message = "Syncing branch product list.";
      $response->product_list = $product_list;

      return response()->json($response);
    }

    public function branchSyncProductListCompleted(Request $request)
    {
      $barcode_array = $request->barcode_array;
      $branch_id = $request->branch_id;

      $branch_detail = Branch::where('token', $branch_id)->first();

      Branch_product::where('product_sync', 0)->where('branch_id', $branch_detail->id)->whereIn('barcode', $barcode_array)->update([
        'product_sync' => 1
      ]);

      $response = new \stdClass();
      $response->error = 0;
      $response->message = "Sync HQ product list completed";

      return response()->json($response);
    }
}
