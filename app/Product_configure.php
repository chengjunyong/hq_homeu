<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product_configure extends Model
{
    protected $table = 'product_configure';
    protected $fillable = 
    [
      'default_price_margin',
      'below_sales_price'
    ];
}
