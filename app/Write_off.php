<?php

namespace App;

use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\SoftDeletes;

class Write_off extends Model
{
  use SoftDeletes;

  protected $table = 'write_off';
  protected $fillable = 
  [
    'seq_no',
    'total_item',
    'total_amount',
    'created_by',
    'write_off_date',
    'completed',
  ];
}
