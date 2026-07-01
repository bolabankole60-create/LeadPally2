<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiPromptTemplate extends Model
{
    protected $fillable = [
        'team_id',
        'key',
        'name',
        'prompt',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
