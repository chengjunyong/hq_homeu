<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tmp_order_list extends Model
{
    protected $table = 'tmp_order_list';
    protected $fillable = 
    [
      'from_branch',
      'to_branch',
      'branch_product_id',
      'department_id',
      'category_id',
      'barcode',
      'product_name',
      'cost',
      'price',
      'order_quantity',
    ];
}
