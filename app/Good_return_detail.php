<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Good_return_detail extends Model
{
    use SoftDeletes;
    protected $table = 'good_return_detail';
    protected $fillable = 
    [
      'good_return_id',
      'barcode',
      'product_name',
      'measurement',
      'cost',
      'quantity',
      'total_cost',
      'update_by',
    ];
}
