<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product_supplier extends Model
{
    protected $table = 'product_supplier';
    protected $fillable = 
    [
      'supplier_id',
      'product_id',
    ];
}
