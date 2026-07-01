<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadScoreRule extends Model
{
    protected $fillable = [
        'team_id',
        'name',
        'field',
        'operator',
        'value',
        'points',
        'is_active',
    ];

    protected $casts = [
        'points' => 'integer',
        'is_active' => 'boolean',
    ];
}
