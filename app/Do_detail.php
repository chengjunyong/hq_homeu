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
      'measurement',
      'cost',
      'quantity',
      'stock_lost_quantity',
      'stock_lost_reason',
      'remark',
      'stock_lost_review',
    ];

    public function product()
    {
        return $this->belongsTo(Product_list::class,'barcode','barcode');
    }

    public function do_list()
    {
        return $this->belongsTo(Do_list::class,'do_number','do_number');
    }
}
