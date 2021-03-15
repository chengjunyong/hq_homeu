<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class user_access_control extends Model
{
    protected $table = 'user_access_control';
    protected $fillable = 
    [
      'user_id',
      'access_control'
    ];
}
