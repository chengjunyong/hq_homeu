<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockApprove extends Model
{
    protected $table = 'stock_approves';
    protected $fillable = 
    [
        'branch_id',
        'updated_by',
    ];
}
