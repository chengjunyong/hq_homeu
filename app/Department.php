<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'branch';
    protected $fillable = 
    [
      'department_name',
    ];
}
