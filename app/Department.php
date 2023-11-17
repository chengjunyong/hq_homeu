<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'department';
    protected $fillable = 
    [
      'department_name',
    ];

    public function categories()
    {
      return $this->hasMany(Category::class, 'department_id');
    }
}
