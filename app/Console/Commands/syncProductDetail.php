<?php

namespace App\Console\Commands;

use App\Branch_product;
use App\Product_list;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class syncProductDetail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:product_detail';

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
        DB::transaction(function () {
            $products = Product_list::get();
            $bar = $this->output->createProgressBar(count($products));
            $bar->start();

            foreach($products as $product){
                Branch_product::where('barcode',$product->barcode)
                                ->update([
                                    'normal_wholesale_price' => $product->normal_wholesale_price,
                                    'normal_wholesale_price2' => $product->normal_wholesale_price2,
                                    'normal_wholesale_quantity' => $product->normal_wholesale_quantity,
                                    'normal_wholesale_quantity2' => $product->normal_wholesale_quantity2,
                                    'normal_wholesale_price3' => $product->normal_wholesale_price3,
                                    'normal_wholesale_price4' => $product->normal_wholesale_price4,
                                    'normal_wholesale_quantity3' => $product->normal_wholesale_quantity3,
                                    'normal_wholesale_quantity4' => $product->normal_wholesale_quantity4,
                                    'normal_wholesale_price5' => $product->normal_wholesale_price5,
                                    'normal_wholesale_price6' => $product->normal_wholesale_price6,
                                    'normal_wholesale_quantity5' => $product->normal_wholesale_quantity5,
                                    'normal_wholesale_quantity6' => $product->normal_wholesale_quantity6,
                                    'normal_wholesale_price7' => $product->normal_wholesale_price7,
                                    'normal_wholesale_quantity7' => $product->normal_wholesale_quantity7,
                                    'promotion_start'=>$product->promotion_start,
                                    'promotion_end'=>$product->promotion_end,
                                    'promotion_price'=>$product->promotion_price,
                                    'wholesale_price'=>$product->wholesale_price,
                                    'wholesale_quantity'=>$product->wholesale_quantity,
                                    'wholesale_start_date'=>$product->wholesale_start_date,
                                    'wholesale_end_date'=>$product->wholesale_end_date,
                                    'wholesale_price2'=>$product->wholesale_price2,
                                    'wholesale_quantity2'=>$product->wholesale_quantity2,
                                ]);
                $bar->advance();
            }

            $bar->finish();
            $this->output->success('Done');
        });
    }
}
