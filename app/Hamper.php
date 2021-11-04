<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hamper extends Model
{
    use SoftDeletes;
    protected $table = 'hamper';
    protected $fillable = 
    [
      'name',
      'price',
      'barcode',
      'product_list',
      'quantity',
      'created_by',
    ];
}
