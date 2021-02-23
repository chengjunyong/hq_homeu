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
      'transaction_no',
      'invoice_no',
      'user_id',
      'subtotal',
      'total_discount',
      'payment',
      'payment_type',
      'payment_type_text',
      'balance',
      'total',
      'void',
      'completed',
      'transaction_date',
      'created_at',
      'updated_at'
    ];
}
