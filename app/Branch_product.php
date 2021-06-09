<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Branch_product extends Model
{
    protected $table = 'branch_product';
    protected $fillable = 
    [
      'branch_id',
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
