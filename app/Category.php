<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'category';
    protected $fillable = 
    [
      'category_name',
      'department_id',
      'category_code',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
