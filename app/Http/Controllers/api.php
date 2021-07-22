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
use App\Branch_shift;
use App\Cash_float;
use App\Refund;
use App\Refund_detail;

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
      $branch_shift = $request->cashier;
      $cash_float = $request->cash_float;
      $refund = $request->refund;
      $refund_detail = $request->refund_detail;

      $branch_id = $request->branch_id;
      $session_list = $request->session_list;

      $previous_transaction_detail = transaction_detail::where('branch_id', $branch_id)->whereIn('session_id', $session_list)->selectRaw('*, sum(quantity + wholesale_quantity) as total_quantity')->groupBy('product_id')->get();

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

        $transaction_product[$product_name]->quantity += ($data['quantity'] + $data['wholesale_quantity']);

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

      // update branch cashier ( shift )
      $branch_shift_query = [];
      $branch_shift_id_array = [];

      foreach($branch_shift as $shift)
      {
        $query = [
          'branch_opening_id' => $shift['id'],
          'branch_id' => $branch_id,
          'ip' => $shift['ip'],
          'cashier_name' => $shift['cashier_name'],
          'opening' => $shift['opening'],
          'opening_by_name' => $shift['opening_by_name'],
          'opening_amount' => $shift['opening_amount'],
          'opening_date_time' => $shift['opening_date_time'],
          'closing' => $shift['closing'],
          'closing_by_name' => $shift['closing_by_name'],
          'closing_amount' => $shift['closing_amount'],
          'calculated_amount' => $shift['calculated_amount'],
          'diff' => $shift['diff'],
          'closing_date_time' => $shift['closing_date_time'],
          'shift_created_at' => $shift['created_at'],
          'created_at' => $now,
          'updated_at' => $now
        ];

        array_push($branch_shift_id_array, $shift['id']);
        array_push($branch_shift_query, $query);
      }

      // prevent duplicate
      Branch_shift::where('branch_id', $branch_id)->whereIn('branch_opening_id', $branch_shift_id_array)->delete();
      Branch_shift::insert($branch_shift_query);
      // end

      // cash float
      $branch_cash_float_query = [];
      $branch_cash_float_id_array = [];
      foreach($cash_float as $cash_float_detail)
      {
        $query = [
          'branch_cash_float_id' => $cash_float_detail['id'],
          'branch_id' => $branch_id,
          'created_by' => $cash_float_detail['created_by'],
          'ip' => $cash_float_detail['ip'],
          'cashier_name' => $cash_float_detail['cashier_name'],
          'branch_opening_id' => $cash_float_detail['opening_id'],
          'type' => $cash_float_detail['type'],
          'amount' => $cash_float_detail['amount'],
          'remarks' => $cash_float_detail['remarks'],
          'cash_float_created_at' => $cash_float_detail['created_at'],
          'created_at' => $now,
          'updated_at' => $now
        ];

        array_push($branch_cash_float_id_array, $cash_float_detail['id']);
        array_push($branch_cash_float_query, $query);
      }

      // prevent duplicate
      Cash_float::where('branch_id', $branch_id)->whereIn('branch_cash_float_id', $branch_cash_float_id_array)->delete();
      Cash_float::insert($branch_cash_float_query);
      // end

      // refund
      $branch_refund_query = [];
      $branch_refund_id_array = [];
      foreach($refund as $refund_info)
      {
        $query = [
          'branch_id' => $branch_id,
          'branch_refund_id' => $refund_info['id'],
          'branch_opening_id' => $refund_info['opening_id'],
          'ip' => $refund_info['ip'],
          'cashier_name' => $refund_info['cashier_name'],
          'created_by' => $refund_info['user_name'],
          'subtotal' => $refund_info['subtotal'],
          'round_off' => $refund_info['round_off'],
          'total' => $refund_info['total'],
          'refund_created_at' => $refund_info['created_at'],
          'created_at' => $now,
          'updated_at' => $now
        ];

        array_push($branch_refund_id_array, $refund_info['id']);
        array_push($branch_refund_query, $query);
      }

      // prevent duplicate
      Refund::where('branch_id', $branch_id)->whereIn('branch_refund_id', $branch_refund_id_array)->delete();
      Refund::insert($branch_refund_query);
      // end

      // refund detail
      $branch_refund_detail_query = [];
      $branch_refund_detail_id_array = [];
      foreach($refund_detail as $refund_detail_info)
      {
        $query = [
          'branch_id' => $branch_id,
          'branch_refund_detail_id' => $refund_detail_info['id'],
          'branch_refund_id' => $refund_detail_info['refund_id'],
          'department_id' => $refund_detail_info['department_id'],
          'category_id' => $refund_detail_info['category_id'],
          'product_id' => $refund_detail_info['product_id'],
          'barcode' => $refund_detail_info['barcode'],
          'product_name' => $refund_detail_info['product_name'],
          'quantity' => $refund_detail_info['quantity'],
          'price' => $refund_detail_info['price'],
          'subtotal' => $refund_detail_info['subtotal'],
          'total' => $refund_detail_info['total'],
          'refund_detail_created_at' => $refund_detail_info['created_at'],
          'created_at' => $now,
          'updated_at' => $now
        ];

        $product_name = "product_".$refund_detail_info['product_id'];
        if(!isset($transaction_product[$product_name]))
        {
          $transaction_product[$product_name] = new \stdClass();
          $transaction_product[$product_name]->barcode = $refund_detail_info['barcode'];
          $transaction_product[$product_name]->quantity = 0;
        }
        
        $transaction_product[$product_name]->quantity -= $refund_detail_info['quantity'];

        array_push($branch_refund_detail_id_array, $refund_detail_info['id']);
        array_push($branch_refund_detail_query, $query);
      }

      $prev_refund_detail = Refund_detail::where('branch_id', $branch_id)->whereIn('branch_refund_detail_id', $branch_refund_detail_id_array)->get();

      foreach($prev_refund_detail as $prev_refund)
      {
        $product_name = "product_".$prev_refund->product_id;
        $transaction_product[$product_name]->quantity += $prev_refund->quantity;
      }

      // prevent duplicate
      Refund_detail::where('branch_id', $branch_id)->whereIn('branch_refund_detail_id', $branch_refund_detail_id_array)->delete();
      Refund_detail::insert($branch_refund_detail_query);
      // end

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
      $product_list = Branch_product::withTrashed()->select('department_id', 'category_id', 'barcode', 'product_name', 'price', 'wholesale_price', 'wholesale_quantity', 'wholesale_start_date', 'wholesale_end_date', 'promotion_start', 'promotion_end', 'promotion_price', 'uom', 'deleted_at')->where('branch_id', $branch_detail->id)->where('product_sync', 0)->get();

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
      
      $product_list = Branch_product::withTrashed()->select('department_id', 'category_id', 'barcode', 'product_name', 'price', 'wholesale_price', 'wholesale_quantity', 'wholesale_start_date', 'wholesale_end_date', 'promotion_start', 'promotion_end', 'promotion_price', 'uom', 'deleted_at')->where('branch_id', $branch_detail->id)->where('product_sync', 0)->get();

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
