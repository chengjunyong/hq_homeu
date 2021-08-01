<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Good_return_detail extends Model
{
    protected $table = 'good_return_detail';
    protected $fillable = 
    [
      'good_return_id',
      'barcode',
      'product_name',
      'cost',
      'quantity',
      'total_cost',
      'update_by',
    ];
}
