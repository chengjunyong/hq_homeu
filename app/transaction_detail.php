<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class transaction_detail extends Model
{
    protected $table = 'transaction_detail';
    protected $fillable = 
    [
      'branch_id',
      'session_id',
      'branch_transaction_detail_id',
      'branch_transaction_id',
      'department_id',
      'category_id',
      'product_id',
      'barcode',
      'product_name',
      'quantity',
      'measurement_type',
      'measurement',
      'price',
      'wholesale_price',
      'wholesale_quantity',
      'discount',
      'subtotal',
      'total',
      'void',
      'transaction_date',
      'transaction_detail_date',
      'created_at', 
      'updated_at'
    ];

    public function transaction()
    {
      return $this->belongsTo(transaction::class,'branch_transaction_id','branch_transaction_id')->orderBy('transaction_date');
    }

    public function scopeListing($query,$branch_id,$start=null,$end=null)
    {
      return $query->where('branch_id',$branch_id);
    }
}
