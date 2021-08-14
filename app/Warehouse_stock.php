<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse_stock extends Model
{
    use SoftDeletes;
    protected $table = 'warehouse_stock';
    protected $fillable = 
    [
      'department_id',
      'category_id',
      'barcode',
      'product_name',
      'uom',
      'measurement',
      'cost',
      'price',
      'quantity',
      'reorder_level',
      'reorder_quantity',
      'unit_type',
      'product_sync',
    ];
}
