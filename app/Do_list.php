<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Do_list extends Model
{
    use SoftDeletes;
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
      'user_id',
      'deleted_by',
    ];

    public function details()
    {
      return $this->hasMany(Do_detail::class,'do_number','do_number');
    }
}
