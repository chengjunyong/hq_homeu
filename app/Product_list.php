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
    ];
}
