<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sub_categories';

    protected $fillable = [
        'name',
        'deleted_by',
        'created_by',
        'updated_by',
    ];

    public function products()
    {
        return $this->hasMany(Product_list::class,'sub_category_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class,'created_by');
    }

    public function updator()
    {
        return $this->belongsTo(User::class,'updated_by');
    }
}
