<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Branch_shift extends Model
{
    protected $table = 'branch_shift';
    protected $fillable = 
    [
      'branch_opening_id',
      'branch_id',
      'ip',
      'cashier_name',
      'opening',
      'opening_by_name',
      'opening_amount',
      'opening_date_time',
      'closing',
      'closing_by_name',
      'closing_amount',
      'calculated_amount',
      'diff',
      'closing_date_time',
      'shift_created_at',
    ];
}
