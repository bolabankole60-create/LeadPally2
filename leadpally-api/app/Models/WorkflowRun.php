<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowRun extends Model
{
    protected $fillable = [
        'workflow_rule_id',
        'team_id',
        'event_name',
        'status',
        'payload',
        'error',
        'executed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'executed_at' => 'datetime',
    ];

    public function rule()
    {
        return $this->belongsTo(WorkflowRule::class, 'workflow_rule_id');
    }
}
