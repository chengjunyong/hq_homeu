<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase_order extends Model
{
    use SoftDeletes;
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
      'user_id',
      'deleted_by',
    ];
}
