<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PipelineStage extends Model
{
    protected $fillable = [
        'pipeline_id',
        'name',
        'position',
        'win_probability',
    ];

    protected $casts = [
        'win_probability' => 'decimal:2',
    ];

    public function pipeline()
    {
        return $this->belongsTo(Pipeline::class);
    }

    public function deals()
    {
        return $this->hasMany(Deal::class);
    }
}
