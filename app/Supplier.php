<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;
    protected $table = 'supplier';
    protected $fillable = 
    [
      'supplier_code',
      'supplier_name',
      'contact_number',
      'email',
      'address1',
      'address2',
      'address3',
      'address4',
    ];
}
