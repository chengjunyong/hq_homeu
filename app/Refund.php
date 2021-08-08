<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    protected $table = 'refund';
    protected $fillable = [
      'branch_id',
      'branch_refund_id',
      'branch_opening_id',
      'ip',
      'cashier_name',
      'created_by',
      'transaction_no',
      'subtotal',
      'round_off',
      'total',
      'refund_created_at',
    ];
}
