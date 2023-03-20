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
use App\branch_stock_history;
use App\Cash_float;
use App\Refund;
use App\Refund_detail;
use App\Delivery;
use App\Delivery_detail;
use App\Hamper;
use App\SchedulerJob;
use App\StockBalanceLog;

class api extends Controller
{
    public function testresult()
    {
    	return "Good";
      // testing message
    }

    public function branchSync(Request $request)
    {
        $now = date('Y-m-d H:i:s');
        $branch_shift = $request->cashier;
        $cash_float = $request->cash_float;
        $branch_id = $request->branch_id;
        $branch_detail = Branch::where('token', $branch_id)->first();
        $refund = $request->refund;
        $refund_detail = $request->refund_detail;
        $actual_branch = Branch::where('token',$branch_id)->first();

        if(!$branch_detail)
        {
          $response = new \stdClass();
          $response->error = 1;
          $response->message = "Branch ID ".$branch_id." not found.";

          return response()->json($response);
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
            'shift_created_at' => date('Y-m-d H:i:s', strtotime($shift['created_at'])),
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
            'cash_float_created_at' => date('Y-m-d H:i:s', strtotime($cash_float_detail['created_at'])),
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
            'transaction_no' => $refund_info['transaction_no'],
            'subtotal' => $refund_info['subtotal'],
            'round_off' => $refund_info['round_off'],
            'total' => $refund_info['total'],
            'refund_created_at' => date('Y-m-d H:i:s', strtotime($refund_info['created_at'])),
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

        $refund_barcode_array = array();
        foreach($refund_detail as $refund_detail_info)
        {
          if(!in_array($refund_detail_info['barcode'], $refund_barcode_array))
          {
            array_push($refund_barcode_array, $refund_detail_info['barcode']);
          }
        }

        $refund_product_list = Branch_product::withTrashed()->where('branch_id', $branch_detail->id)->whereIn('barcode', $refund_barcode_array)->get();

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
            'transaction_no' => $refund_detail_info['transaction_no'],
            'product_id' => $refund_detail_info['product_id'],
            'barcode' => $refund_detail_info['barcode'],
            'product_name' => $refund_detail_info['product_name'],
            'quantity' => $refund_detail_info['quantity'],
            'measurement_type' => $refund_detail_info['measurement_type'],
            'measurement' => $refund_detail_info['measurement'],
            'price' => $refund_detail_info['price'],
            'subtotal' => $refund_detail_info['subtotal'],
            'total' => $refund_detail_info['total'],
            'refund_detail_created_at' => date('Y-m-d H:i:s', strtotime($refund_detail_info['created_at'])),
            'created_at' => $now,
            'updated_at' => $now
          ];

          $product_name = "product_".str_replace(" ", "", $refund_detail_info['barcode']);
          if(!isset($transaction_product[$product_name]))
          {
            $transaction_product[$product_name] = new \stdClass();
            $transaction_product[$product_name]->barcode = $refund_detail_info['barcode'];
            $transaction_product[$product_name]->quantity = 0;
            $transaction_product[$product_name]->last_stock_updated_at = null;

            $product_detail = null;
            foreach($refund_product_list as $refund_product_detail)
            {
              if($refund_product_detail->barcode == $refund_detail_info['barcode'])
              {
                $product_detail = $refund_product_detail;
                break; 
              }
            }

            if($product_detail)
            {
              if($product_detail->last_stock_updated_at)
              {
                $transaction_product[$product_name]->last_stock_updated_at = $product_detail->last_stock_updated_at;
              }
            }

          }
          
          if(!$transaction_product[$product_name]->last_stock_updated_at || ($transaction_product[$product_name]->last_stock_updated_at <= date('Y-m-d H:i:s', strtotime($refund_detail_info['created_at'])) ))
          {
            $transaction_product[$product_name]->quantity += ($refund_detail_info['quantity'] * $refund_detail_info['measurement']);
          }

          array_push($branch_refund_detail_id_array, $refund_detail_info['id']);
          array_push($branch_refund_detail_query, $query);

          // Branch_product::withTrashed()
          //                 ->where('branch_id',$actual_branch->id)
          //                 ->where('barcode',$refund_detail_info['barcode'])
          //                 ->increment('quantity',$refund_detail_info['quantity']);
        }

        $prev_refund_detail = Refund_detail::where('branch_id', $branch_id)->whereIn('branch_refund_detail_id', $branch_refund_detail_id_array)->get();

        foreach($prev_refund_detail as $prev_refund)
        {
          $product_name = "product_".str_replace(" ", "", $prev_refund->barcode);
          if(!$prev_refund->measurement)
          {
            $prev_refund->measurement = 1;
          }

          if(!$transaction_product[$product_name]->last_stock_updated_at || ($transaction_product[$product_name]->last_stock_updated_at <= date('Y-m-d H:i:s', strtotime($prev_refund->refund_detail_created_at)) ))
          {
            $transaction_product[$product_name]->quantity -= ($prev_refund->quantity * $prev_refund->measurement);
          }
        }

        // prevent duplicate
        Refund_detail::where('branch_id', $branch_id)->whereIn('branch_refund_detail_id', $branch_refund_detail_id_array)->delete();
        Refund_detail::insert($branch_refund_detail_query);
        // end

        // New branch sync logic
        $branch = Branch::where('token',$request->branch_id)->first();
        $existing = SchedulerJob::where('branch_id',$branch->id)->get();

        foreach($request->transaction as $result){

          $validate = $existing->where('session_id',$result['session_id'])
                              ->where('entity_type','sales')
                              ->where('entity_id',$result['id']);

          if($validate->count() <= 0){
            SchedulerJob::create([
              'branch_id' => $branch->id,
              'session_id' => $result['session_id'],
              'entity_type' => 'sales',
              'entity_id' => $result['id'],
              'transaction' => json_encode($result),
              'transaction_detail' => json_encode($result['transaction_details']),
              'sync' => 0,
            ]);
          }
        }
        // End branch sync logic

        // more than 10000, php will return error
        $product_list = Branch_product::withTrashed()->where('branch_id', $branch_detail->id)->where('product_sync', 0)->get();
        $hamper_list = Hamper::get();

        $response = new \stdClass();
        $response->error = 0;
        $response->message = "Transaction sync completed";
        $response->product_list = $product_list;
        $response->hamper_list = $hamper_list;

        return response()->json($response);
    }

    public function newBranchSync(Request $request)
    {
      $branch = Branch::where('token',$request->branch_id)->first();
      $existing = SchedulerJob::where('branch_id',$branch->id)->get();

      foreach($request->transaction as $result){

        $validate = $existing->where('session_id',$result['session_id'])
                            ->where('entity_type','sales')
                            ->where('entity_id',$result['id']);

        if($validate->count() <= 0){
          SchedulerJob::create([
            'branch_id' => $branch->id,
            'session_id' => $result['session_id'],
            'entity_type' => 'sales',
            'entity_id' => $result['id'],
            'transaction' => json_encode($result),
            'transaction_detail' => json_encode($result['transaction_details']),
            'sync' => 0,
          ]);
        }
      }

      // foreach($request->cashier as $result){

      //   $validate = $existing->where('session_id',$result['session_id'])
      //                         ->where('entity_type','cashier')
      //                         ->where('entity_id',$result['id']);

      //   if($validate->count() <= 0){
      //     SchedulerJob::create([
      //       'branch_id' => $branch->id,
      //       'session_id' => $result['session_id'],
      //       'entity_type' => 'cashier',
      //       'entity_id' => $result['id'],
      //       'records' => json_encode($result),
      //       'sync' => 0,
      //     ]);
      //   }
      // }

      // foreach($request->cash_float as $result){

      //   $validate = $existing->where('session_id',$result['session_id'])
      //                       ->where('entity_type','cash_float')
      //                       ->where('entity_id',$result['id']);

      //   if($validate->count() <= 0){
      //     SchedulerJob::create([
      //       'branch_id' => $branch->id,
      //       'session_id' => $result['session_id'],
      //       'entity_type' => 'cash_float',
      //       'entity_id' => $result['id'],
      //       'records' => json_encode($result),
      //       'sync' => 0,
      //     ]);
      //   }
      // }

      // foreach($request->refund as $result){

      //   $validate = $existing->where('session_id',$result['session_id'])
      //                       ->where('entity_type','refund')
      //                       ->where('entity_id',$result['id']);
                            
      //   if($validate->count() <= 0){
      //     SchedulerJob::create([
      //       'branch_id' => $branch->id,
      //       'session_id' => $result['session_id'],
      //       'entity_type' => 'refund',
      //       'entity_id' => $result['id'],
      //       'records' => json_encode($result),
      //       'sync' => 0,
      //     ]);
      //   }
      // }
            
      // foreach($request->delivery as $result){

      //   $validate = $existing->where('session_id',$result['session_id'])
      //                       ->where('entity_type','delivery')  
      //                       ->where('entity_id',$result['id']);

      //   if($validate->count() <= 0){
      //     SchedulerJob::create([
      //       'branch_id' => $branch->id,
      //       'session_id' => $result['session_id'],
      //       'entity_type' => 'delivery',
      //       'entity_id' => $result['id'],
      //       'records' => json_encode($result),
      //       'sync' => 0,
      //     ]);
      //   }
      // }

      $product_list = Branch_product::withTrashed()->where('branch_id', $branch->id)->where('product_sync', 0)->get();
      $hamper_list = Hamper::get();

      $response = new \stdClass();
      $response->error = 0;
      $response->message = "Transaction sync completed";
      $response->product_list = $product_list;
      $response->hamper_list = $hamper_list;

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
      
      $product_list = Branch_product::withTrashed()->where('branch_id', $branch_detail->id)->where('product_sync', 0)->get();
      $hamper_list = Hamper::get();
      $voucher_list = Voucher::get();

      $response = new \stdClass();
      $response->error = 0;
      $response->message = "Syncing branch product list.";
      $response->product_list = $product_list;
      $response->voucher_list = $voucher_list;
      $response->hamper_list = $hamper_list;

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

    public function dailyRecordStockBalance()
    {
      //Stock Kawalan Category id
      $department_id = 21;

      $branch_list = Branch_product::where('department_id',$department_id)
                                    ->orderBy('branch_id','ASC')
                                    ->orderBy('barcode','ASC')
                                    ->get();

      foreach($branch_list as $result){
        StockBalanceLog::create([
          'branch_id' => $result->branch_id,
          'barcode' => $result->barcode,
          'balance' => $result->quantity ?? 0,
        ]);
      }

      return "Done";
    }

    public function recalculateBranchStock()
    {
      $transactions = SchedulerJob::with('branch')
                                    ->where('entity_type','sales')
                                    ->where('sync',0)
                                    ->limit(1000)
                                    ->get();

      foreach($transactions as $transaction){

            $data = json_decode($transaction->transaction,true);
            $transactionDate = $data['transaction_date'];
            Transaction::create([
                'branch_transaction_id' => $data['id'],
                'branch_id' => $transaction->branch->token,
                'session_id' => $data['session_id'],
                'ip' => $data['ip'],
                'cashier_name' => $data['cashier_name'],
                'transaction_no' => $data['transaction_no'],
                'reference_no' => $data['reference_no'],
                'user_id' => $data['user_id'],
                'user_name' => $data['user_name'],
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
                'transaction_date' => $transactionDate,
            ]);

            foreach($data['transaction_details'] as $details){
              $branchItem = Branch_product::where('branch_id',$transaction->branch->id)
                                          ->where('barcode',$details['barcode'])
                                          ->withTrashed()
                                          ->orderBy('created_at','DESC')
                                          ->first();

                Transaction_detail::create([
                    'branch_id' => $transaction->branch->token,
                    'session_id' => $data['session_id'],
                    'branch_transaction_detail_id' => $details['id'],
                    'branch_transaction_id' => $details['transaction_id'],
                    'department_id' => $details['department_id'] ?? 1,
                    'category_id' => $details['category_id'] ?? 1,
                    'product_id' => $branchItem->id,
                    'barcode' => $branchItem->barcode,
                    'product_name' => $branchItem->product_name,
                    'quantity' => $details['quantity'],
                    'measurement_type' => $details['measurement_type'],
                    'measurement' => $details['measurement'],
                    'product_info' => $details['product_info'],
                    'product_type' => $details['product_type'],
                    'price' => $details['price'],
                    'wholesale_price' => $details['wholesale_price'],
                    'wholesale_quantity' => $details['wholesale_quantity'],
                    'discount' => $details['discount'],
                    'subtotal' => $details['subtotal'],
                    'total' => $details['total'],
                    'transaction_date' => $transactionDate,
                    'transaction_detail_date' => $transactionDate,
                ]);

                $stockCheckHistory = branch_stock_history::where('branch_id',$transaction->branch->id)
                                                            ->where('barcode',$branchItem->barcode)
                                                            ->orderBy('created_at','DESC')
                                                            ->first();
                                                
                if($stockCheckHistory == null || $stockCheckHistory->created_at < $transactionDate){
                    $branchItem->decrement('quantity',$details['quantity']);
                }
            }
            $transaction->update(['sync' => 1]);
            $bar->advance();
        }

      exit('Done');
    }

}
