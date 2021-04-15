<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Warehouse_restock_history extends Model
{
    protected $table = 'warehouse_restock_history';
    protected $fillable = 
    [
      'po_id',
      'po_number',
      'supplier_id',
      'supplier_name',
      'supplier_code',
      'invoice_number',
    ];
}
