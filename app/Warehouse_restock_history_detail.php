<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Warehouse_restock_history_detail extends Model
{
    protected $table = 'warehouse_restock_history_detail';
    protected $fillable = 
    [
      'warehouse_history_id',
      'product_id',
      'barcode',
      'product_name',
      'measurement',
      'cost',
      'quantity',
      'remark',
    ];
}
