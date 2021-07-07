<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaction;
use App\Transaction_detail;
use App\Branch_product;
use App\Branch;
use App\Product_list;
use Illuminate\Support\Facades\DB;
use App\Warehouse_stock;
use App\Voucher;

class api extends Controller
{
    public function testresult()
    {
    	return "Good";
    }

    public function branchSync(Request $request)
    {
      $now = date('Y-m-d H:i:s');
      $transaction = $request->transaction;
      $transaction_detail = $request->transaction_detail;

      $branch_id = $request->branch_id;
      $session_list = $request->session_list;

      $previous_transaction_detail = transaction_detail::where('branch_id', $branch_id)->whereIn('session_id', $session_list)->selectRaw('*, sum(quantity) as total_quantity')->groupBy('product_id')->get();

      transaction::where('branch_id', $branch_id)->whereIn('session_id', $session_list)->delete();
      transaction_detail::where('branch_id', $branch_id)->whereIn('session_id', $session_list)->delete();
      
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
          'session_id' => $data['session_id'],
          'ip' => $data['ip'],
          'cashier_name' => $data['cashier_name'],
          'transaction_no' => $data['transaction_no'],
          'reference_no' => $data['reference_no'],
          'user_id' => $data['user_id'],
          'subtotal' => $data['subtotal'],
          'total_discount' => $data['total_discount'],
          'voucher_code' => $data['voucher_code'],
          'payment' => $data['payment'],
          'payment_type' => $data['payment_type'],
          'payment_type_text' => $data['payment_type_text'],
          'balance' => $data['balance'],
          'total' => $data['total'],
          'round_off' => $data['round_off'],
          'void' => $data['void'],
          'completed' => $data['completed'],
          'transaction_date' => $data['transaction_date'],
          'created_at' => $now,
          'updated_at' => $now
        ];

        array_push($transaction_query, $query);
      }

      $transaction_query = array_chunk($transaction_query,500);
      foreach($transaction_query as $query){
        Transaction::insert($query);
      }

      $transaction_product = array();

      $transaction_detail_query = [];
      foreach($transaction_detail as $data)
      {
        $query = [
          'branch_id' => $branch_id,
          'session_id' => $data['session_id'],
          'branch_transaction_detail_id' => $data['id'],
          'branch_transaction_id' => $data['transaction_id'],
          'department_id' => $data['department_id'],
          'category_id' => $data['category_id'],
          'product_id' => $data['product_id'],
          'barcode' => $data['barcode'],
          'product_name' => $data['product_name'],
          'quantity' => $data['quantity'],
          'price' => $data['price'],
          'wholesale_quantity' => $data['wholesale_quantity'],
          'wholesale_price' => $data['wholesale_price'],
          'discount' => $data['discount'],
          'subtotal' => $data['subtotal'],
          'total' => $data['total'],
          'void' => $data['void'],
          'created_at' => $now,
          'updated_at' => $now
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
        Transaction_detail::insert($query);
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
      $product_list = Branch_product::withTrashed()->select('department_id', 'category_id', 'barcode', 'product_name', 'price', 'promotion_start', 'promotion_end', 'promotion_price', 'uom', 'deleted_at')->where('branch_id', $branch_detail->id)->where('product_sync', 0)->get();

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

      Branch_product::withTrashed()->where('product_sync', 0)->where('branch_id', $branch_detail->id)->whereIn('barcode', $barcode_array)->update([
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
      
      $product_list = Branch_product::withTrashed()->select('department_id', 'category_id', 'barcode', 'product_name', 'price', 'promotion_start', 'promotion_end', 'promotion_price', 'uom', 'deleted_at')->where('branch_id', $branch_detail->id)->where('product_sync', 0)->get();

      $voucher_list = Voucher::get();

      $response = new \stdClass();
      $response->error = 0;
      $response->message = "Syncing branch product list.";
      $response->product_list = $product_list;
      $response->voucher_list = $voucher_list;

      return response()->json($response);
    }

    public function branchSyncProductListCompleted(Request $request)
    {
      $barcode_array = $request->barcode_array;
      $branch_id = $request->branch_id;

      $branch_detail = Branch::where('token', $branch_id)->first();

      Branch_product::withTrashed()->where('product_sync', 0)->where('branch_id', $branch_detail->id)->whereIn('barcode', $barcode_array)->update([
        'product_sync' => 1
      ]);

      $response = new \stdClass();
      $response->error = 0;
      $response->message = "Sync HQ product list completed";

      return response()->json($response);
    }

    public function CronPriceSync()
    {
      try{
        $list = Product_list::whereRaw("schedule_date = DATE(NOW())")->get();
        foreach($list as $result){
          Branch_product::where('barcode',$result->barcode)
                        ->update([
                            'price' => $result->schedule_price,
                            'product_sync' => 0,
                          ]);

          Warehouse_stock::where('barcode',$result->barcode)
                        ->update([
                            'price' => $result->schedule_price,
                            'product_sync' => 0,
                          ]);
        }

        DB::select(DB::raw("UPDATE product_list SET price = schedule_price WHERE schedule_date = DATE(NOW())"));

        return "Success";

      }catch(Throwable $e){

        return "Something Wrong";

      }
    }
}
