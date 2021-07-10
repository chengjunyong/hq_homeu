<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice_purchase extends Model
{
    protected $table = 'invoice_purchase';
    protected $fillable = 
    [
      'invoice_date',
      'invoice_no',
      'total_item',
      'total_cost',
      'total_different_item',
      'supplier_id',
      'supplier_name',
      'creator_id',
      'creator_name',
      'completed',
    ];
}
