<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    protected $table = 'delivery';
    protected $fillable = 
    [
      'branch_delivery_id',
      'branch_id',
      'session_id',
      'opening_id',
      'ip',
      'cashier_name',
      'transaction_no',
      'reference_no',
      'user_id',
      'user_name',
      'subtotal',
      'total_discount',
      'voucher_code',
      'delivery_type',
      'total',
      'round_off',
      'completed',
      'completed_by',
      'delivery_created_at',
      'created_at',
      'updated_at',
    ];
}
