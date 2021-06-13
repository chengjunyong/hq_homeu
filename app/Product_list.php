<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product_list extends Model
{
    protected $table = 'product_list';
    protected $fillable = 
    [
      'department_id',
      'category_id',
      'barcode',
      'product_name',
      'cost',
      'price',
      'quantity',
      'reorder_level',
      'recommend_quantity',
      'unit_type',
      'product_sync',
      'schedule_date',
      'schedule_price',
      'promotion_start',
      'promotion_end',
      'promotion_price',
    ];
}
