<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hamper extends Model
{
    use SoftDeletes;
    protected $table = 'hamper';
    protected $fillable = 
    [
      'branch_id',
      'name',
      'price',
      'barcode',
      'product_list',
      'quantity',
      'created_by',
    ];

    public function branch()
    {
      return $this->belongsTo(Branch::class,'branch_id');
    }

    public function user()
    {
      return $this->belongsTo(User::class,'created_by');
    }
}
