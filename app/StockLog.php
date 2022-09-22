<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockLog extends Model
{
    protected $table = 'stock_logs';
    protected $connection= 'mysql2';
    protected $fillable = 
    [
        'barcode',
        'product_name',
        'wb1_qty',
        'wb2_qty',
        'pm1_qty',
        'pm2_qty',
        'pc_qty',
        'bachok_qty',
        'warehouse_qty',
        'batch',
    ];
}
