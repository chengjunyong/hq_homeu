<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice_purchase extends Model
{   
    use SoftDeletes;
    protected $table = 'invoice_purchase';
    protected $fillable = 
    [
      'reference_no',
      'invoice_date',
      'invoice_no',
      'destination',
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
