<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockApproveDetail extends Model
{
    protected $table = 'stock_approve_details';
    protected $fillable = 
    [
        'product_id',
        'barcode',
        'stock_count',
    ];
}
