<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tmp_purchase_list extends Model
{
    protected $table = 'tmp_purchase_list';
    protected $fillable = 
    [
      'supplier_id',
      'supplier_name',
      'warehouse_stock_id',
      'department_id',
      'category_id',
      'barcode',
      'product_name',
      'measurement',
      'cost',
      'price',
      'order_quantity',
      'user_id',
    ];
}
