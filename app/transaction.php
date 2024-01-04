<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class transaction extends Model
{
    protected $table = 'transaction';
    protected $fillable = 
    [
      'branch_transaction_id',
      'branch_id',
      'session_id',
      'ip',
      'cashier_name',
      'transaction_no',
      'invoice_no',
      'reference_no',
      'user_id',
      'user_name',
      'subtotal',
      'total_discount',
      'voucher_code',
      'payment',
      'payment_type',
      'payment_type_text',
      'balance',
      'total',
      'round_off',
      'void',
      'completed',
      'transaction_date',
      'created_at',
      'updated_at'
    ];
}
