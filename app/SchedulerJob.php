<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchedulerJob extends Model
{
    protected $table = 'scheduler_job';
    protected $fillable = [
        'branch_id',
        'session_id',
        'entity_type',
        'entity_id',
        'transaction_id',
        'transaction',
        'transaction_detail',
        'records',
        'sync',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
