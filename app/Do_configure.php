<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Do_configure extends Model
{
    protected $table = 'do_configure';
    protected $fillable = 
    [
      'current_do_number',
      'next_do_number',
      'do_prefix_number',
      'year'
    ];
}
