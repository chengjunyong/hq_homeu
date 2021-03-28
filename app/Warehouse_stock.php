<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Warehouse_stock extends Model
{
    protected $table = 'warehouse_stock';
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
      'reorder_quantity',
      'unit_type',
      'product_sync',
    ];
}
