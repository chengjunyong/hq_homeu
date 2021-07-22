<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Refund_detail extends Model
{
    protected $table = 'refund_detail';
    protected $fillable = [
      'branch_id',
      'branch_refund_detail_id',
      'branch_refund_id',
      'department_id',
      'category_id',
      'product_id',
      'barcode',
      'product_name',
      'quantity',
      'price',
      'subtotal',
      'total',
      'refund_detail_created_at',
    ];
}
