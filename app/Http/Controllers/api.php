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
