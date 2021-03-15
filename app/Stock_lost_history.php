<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stock_lost_history extends Model
{
    protected $table = 'stock_lost_history';
    protected $fillable = 
    [
	    'stock_lost_id',
		'do_number',
		'barcode',
		'product_name',
		'price_per_unit',
		'lost_quantity',
		'total',
		'remark',
    ];
}
