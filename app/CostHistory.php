<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CostHistory extends Model
{
    protected $table = 'cost_history';
    protected $fillable = [
        'product_id',
        'barcode',
        'product_name',
        'cost',
    ];
}
