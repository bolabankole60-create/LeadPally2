<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignAudience extends Model
{
    protected $fillable = [
        'campaign_id',
        'lead_id',
        'status',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
