<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cash_float extends Model
{
    protected $table = 'cash_float';
    protected $fillable = [
      'branch_cash_float_id',
      'branch_id',
      'created_by',
      'ip',
      'cashier_name',
      'branch_opening_id',
      'type',
      'amount',
      'remarks',
      'cash_float_created_at',
    ];
}
