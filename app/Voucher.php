<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $table = 'voucher';
    protected $fillable = 
    [
      'name',
      'code',
      'type',
      'amount',
      'active',
      'creator_id',
      'creator_name'
    ];
}
