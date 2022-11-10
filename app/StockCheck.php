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
        'product_name',
        'user_id',
        'stock_count',
        'raw',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class,'destination');
    }

    public function branchProduct()
    {
        return $this->belongsTo(Branch_product::class,'product_id','id');
    }

    public function warehouseProduct()
    {
        return $this->belongsTo(Warehouse_stock::class,'product_id','id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
