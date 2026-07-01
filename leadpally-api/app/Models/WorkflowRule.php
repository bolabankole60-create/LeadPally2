<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowRule extends Model
{
    protected $fillable = [
        'team_id',
        'user_id',
        'name',
        'event_name',
        'criteria',
        'steps',
        'is_active',
    ];

    protected $casts = [
        'criteria' => 'array',
        'steps' => 'array',
        'is_active' => 'boolean',
    ];

    public function runs()
    {
        return $this->hasMany(WorkflowRun::class);
    }
}
