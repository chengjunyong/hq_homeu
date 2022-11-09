<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockCheck extends Model
{
    protected $table = 'stock_checks';
    protected $fillable = 
    [
        'destination',
        'product_id',
        'barcode',
        'user_id',
        'stock_count',
        'raw',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class,'destination');
    }

    public function product()
    {
        return $this->belongsTo(Product_list::class,'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
