<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product_history extends Model
{
    protected $table = 'product_history';
    protected $fillable = ['product_id','barcode','previous_value','current_value','created_by','creator_name'];
}
