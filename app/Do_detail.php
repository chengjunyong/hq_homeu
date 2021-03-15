<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Do_detail extends Model
{
    protected $table = 'do_detail';
    protected $fillable = 
    [
      'do_number',
      'product_id',
      'barcode',
      'product_name',
      'price',
      'quantity',
      'stock_lost_quantity',
      'stock_lost_reason',
      'remark',
      'stock_lost_review',
    ];
}
