<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'product';
    protected $fillable = 
    [
      'branch_id',
      'barcode',
      'product_name',
      'price',
      'quantity',
    ];
}
