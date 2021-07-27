<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tmp_invoice_purchase extends Model
{
    protected $table = 'tmp_invoice_purchase';
    protected $fillable = 
    [
      'barcode',
      'product_name',
      'cost',
      'quantity',
      'total',
      'user_id',
    ];
}
