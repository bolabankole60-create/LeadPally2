<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TeamInvitation extends Model {
    protected $fillable=['team_id','invited_by','email','role','token','accepted_at','expires_at'];
    protected $casts=['accepted_at'=>'datetime','expires_at'=>'datetime'];
    public function team(){ return $this->belongsTo(Team::class); }
    public function inviter(){ return $this->belongsTo(User::class,'invited_by'); }
}
