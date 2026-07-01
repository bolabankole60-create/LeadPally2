<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = [
        'team_id',
        'user_id',
        'name',
        'channel',
        'status',
        'subject',
        'body',
        'scheduled_at',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function audiences()
    {
        return $this->hasMany(CampaignAudience::class);
    }

    public function leads()
    {
        return $this->belongsToMany(Lead::class, 'campaign_audiences')
            ->withPivot(['status', 'sent_at'])
            ->withTimestamps();
    }
}
