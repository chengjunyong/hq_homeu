<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchedulerJob extends Model
{
    protected $table = 'scheduler_job';
    protected $fillable = [
        'branch_id',
        'session_id',
        'transaction_id',
        'transaction',
        'transaction_detail',
        'sync',
    ];
}
