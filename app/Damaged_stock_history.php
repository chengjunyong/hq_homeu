<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Damaged_stock_history extends Model
{
    protected $table = 'damaged_stock_history';
    protected $fillable = 
    [
	   'gr_number',
		'do_number',
    'supplier_id',
		'barcode',
		'product_name',
		'price_per_unit',
		'lost_quantity',
		'total',
		'remark',
    ];
}
