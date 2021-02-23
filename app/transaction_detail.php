<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class transaction_detail extends Model
{
    protected $table = 'transaction_detail';
    protected $fillable = 
    [
      'branch_id',
      'session_id',
      'branch_transaction_detail_id',
      'branch_transaction_id',
      'product_id',
      'barcode',
      'product_name',
      'quantity',
      'price',
      'discount',
      'subtotal',
      'total',
      'void',
      'created_at', 
      'updated_at'
    ];
}
