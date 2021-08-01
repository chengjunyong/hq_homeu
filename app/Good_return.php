<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Good_return extends Model
{
    use SoftDeletes;
    protected $table = 'good_return';
    protected $fillable = 
    [
      'gr_no',
      'gr_date',
      'ref_no',
      'total_quantity',
      'total_cost',
      'total_different_item',
      'supplier_id',
      'supplier_name',
      'creator_id',
      'creator_name',
      'deleted_by',
    ];
}
