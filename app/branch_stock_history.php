<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class branch_stock_history extends Model
{

  protected $table = 'branch_stock_history';
  protected $fillable = 
  [
    'user_id',
    'user_name',
    'branch_id',
    'branch_token',
    'branch_product_id',
    'barcode',
    'product_name',
    'old_stock_count',
    'new_stock_count',
    'difference_count',
  ];


}
