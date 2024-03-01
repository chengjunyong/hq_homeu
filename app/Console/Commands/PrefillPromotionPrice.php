<?php

namespace App\Console\Commands;

use App\Branch_product;
use App\Product_list;
use Illuminate\Console\Command;

class PrefillPromotionPrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prefill:promotion_price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $today = date("Y-m-d");
        $products = Product_list::whereBetween('next_promotion_start',[$today.' 00:00:00',$today." 23:59:59"])->get();

        foreach($products as $product){
            $barcode = $product->barcode;
            Branch_product::where('barcode',$barcode)
                            ->update([
                                'promotion_start' => $product->next_promotion_start,
                                'promotion_end' => $product->next_promotion_end,
                                'promotion_price' => $product->next_promotion_price,
                                'product_sync' => 0,
                            ]);

            $product->update([
                'promotion_start' => $product->next_promotion_start,
                'promotion_end' => $product->next_promotion_end,
                'promotion_price' => $product->next_promotion_price,
                'product_sync'=>0,
            ]);
        }

        $this->output->success('Done');
    }
}
