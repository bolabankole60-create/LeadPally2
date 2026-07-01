<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    protected $fillable = [
        'team_id',
        'lead_id',
        'pipeline_id',
        'pipeline_stage_id',
        'title',
        'value',
        'currency',
        'status',
        'expected_close_at',
        'closed_at',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'expected_close_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function pipeline()
    {
        return $this->belongsTo(Pipeline::class);
    }

    public function stage()
    {
        return $this->belongsTo(PipelineStage::class, 'pipeline_stage_id');
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
