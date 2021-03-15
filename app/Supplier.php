<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
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
