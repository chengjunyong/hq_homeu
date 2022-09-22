<?php

namespace App\Http\Controllers;

use App\Branch_product;
use App\Product_list;
use App\StockLog;
use App\Warehouse_stock;
use Illuminate\Http\Request;

class StockLogController extends Controller
{
    public function index()
    {
        $branch_item = Branch_product::all();
        $warehouse = Warehouse_stock::all();
        $products = Product_list::all();
        $batch = date("Y-m-d",strtotime('now -1 days'));

        foreach($products as $result){
            StockLog::updateOrCreate(
                [
                    'barcode' => $result->barcode,
                    'product_name' => $result->product_name,
                    'batch' => $batch,
                ],
                [
                    'wb1_qty' => $branch_item->where('barcode',$result->barcode)->where('branch_id',1)->first()->quantity ?? 0,
                    'wb2_qty' => $branch_item->where('barcode',$result->barcode)->where('branch_id',3)->first()->quantity ?? 0,
                    'pm1_qty' => $branch_item->where('barcode',$result->barcode)->where('branch_id',6)->first()->quantity ?? 0,
                    'pm2_qty' => $branch_item->where('barcode',$result->barcode)->where('branch_id',7)->first()->quantity ?? 0,
                    'bachok_qty' => $branch_item->where('barcode',$result->barcode)->where('branch_id',4)->first()->quantity ?? 0,
                    'pc_qty' => $branch_item->where('barcode',$result->barcode)->where('branch_id',5)->first()->quantity ?? 0,
                    'warehouse_qty' => $warehouse->where('barcode',$result->barcode)->first()->quantity ?? 0,
                ]
            );
        }

        exit('Finish');
    }
}
