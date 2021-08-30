<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Write_off_detail extends Model
{
  protected $table = 'write_off_detail';
  protected $fillable = 
  [
    'write_off_id',
    'seq_no',
    'barcode',
    'product_name',
    'quantity',
    'cost',
    'total',
    'created_by',
    'completed',
  ];
}
