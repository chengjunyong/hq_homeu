<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Purchase_order extends Model
{
    protected $table = 'purchase_order';
    protected $fillable = 
    [
      'po_number',
      'supplier_id',
      'supplier_code',
			'supplier_name',
			'total_quantity_items',
			'total_amount',
			'stock_lost',
      'issue_date',
			'completed',
    ];
}
