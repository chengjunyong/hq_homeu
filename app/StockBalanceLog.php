<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockBalanceLog extends Model
{
    protected $table = 'stock_balance_log';
    protected $fillable = 
    [
        'branch_id',
        'barcode',
        'balance'
    ];
}
