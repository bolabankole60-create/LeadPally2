<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiConversation extends Model
{
    protected $fillable = [
        'team_id',
        'user_id',
        'lead_id',
        'title',
        'purpose',
    ];

    public function messages()
    {
        return $this->hasMany(AiMessage::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
