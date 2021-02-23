<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Do_list extends Model
{
    protected $table = 'do_list';
    protected $fillable = 
    [
      'do_number',
      'from',
      'from_branch_id',
      'to',
      'to_branch_id',
      'total_item',
      'description',
      'completed',
      'completed_time',
      'stock_lost',
    ];
}
