<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadScoreHistory extends Model
{
    protected $fillable = [
        'lead_id',
        'team_id',
        'old_score',
        'new_score',
        'old_temperature',
        'new_temperature',
        'matched_rules',
    ];

    protected $casts = [
        'matched_rules' => 'array',
    ];
}
