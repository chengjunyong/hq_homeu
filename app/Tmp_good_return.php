<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tmp_good_return extends Model
{
    protected $table = 'tmp_good_return';
    protected $fillable = 
    [
      'barcode',
      'product_name',
      'measurement',
      'cost',
      'quantity',
      'total',
      'user_id',
    ];
}
