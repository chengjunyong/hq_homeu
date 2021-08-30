<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Delivery_detail extends Model
{
    protected $table = 'delivery_detail';
    protected $fillable = 
    [
      'branch_id',
      'branch_delivery_detail_id',
      'branch_delivery_id',
      'department_id',
      'category_id',
      'product_id',
      'barcode',
      'product_name',
      'quantity',
      'measurement_type',
      'measurement',
      'price',
      'wholesale_price',
      'discount',
      'subtotal',
      'total',
      'delivery_detail_created_at',
      'created_at',
      'updated_at',
    ];
}
