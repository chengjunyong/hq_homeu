<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Purchase_order_detail extends Model
{
    protected $table = 'purchase_order_detail';
    protected $fillable = 
    [
      'po_id',
			'po_number',
			'product_id',
			'barcode',
			'product_name',
      'measurement',
			'cost',
			'quantity',
			'received',
			'reason',
    ];
}
