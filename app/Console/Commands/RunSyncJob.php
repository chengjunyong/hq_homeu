<?php

namespace App\Console\Commands;

use App\transaction;
use App\SchedulerJob;
use App\Branch_product;
use App\transaction_detail;

use App\Branch_stock_history;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RunSyncJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:syncStock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Stock';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $transactions = SchedulerJob::with('branch')
                                    ->where('entity_type','sales')
                                    ->where('sync',0)
                                    ->get();

        $bar = $this->output->createProgressBar(count($transactions));
        $bar->start();

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

        $bar->finish();
        $this->output->success('Done');

        Log::info("Stock sync is working fine!");
    }
}
