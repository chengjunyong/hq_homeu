<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice_purchase_detail extends Model
{
    protected $table = 'invoice_purchase_detail';
    protected $fillable = 
    [
      'invoice_purchase_id',
      'barcode',
      'product_name',
      'cost',
      'quantity',
      'total_cost',
      'update_by',
    ];
}
