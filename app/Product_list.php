<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product_list extends Model
{
    use SoftDeletes;
    protected $table = 'product_list';
    protected $fillable = 
    [
      'department_id',
      'category_id',
      'barcode',
      'product_name',
      'uom',
      'cost',
      'price',
      'quantity',
      'normal_wholesale_price',
      'normal_wholesale_price2',
      'normal_wholesale_quantity',
      'normal_wholesale_quantity2',
      'normal_wholesale_price3',
      'normal_wholesale_price4',
      'normal_wholesale_quantity3',
      'normal_wholesale_quantity4',
      'normal_wholesale_price5',
      'normal_wholesale_price6',
      'normal_wholesale_quantity5',
      'normal_wholesale_quantity6',
      'normal_wholesale_price7',
      'normal_wholesale_quantity7',
      'reorder_level',
      'recommend_quantity',
      'unit_type',
      'product_sync',
      'schedule_date',
      'schedule_price',
      'promotion_start',
      'promotion_end',
      'promotion_price',
      'wholesale_price',
      'wholesale_quantity',
      'wholesale_start_date',
      'wholesale_end_date',
      'wholesale_price2',
      'wholesale_quantity2',
      'deleted_by',
    ];
}
