<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Lead extends Model {
    protected $fillable=['team_id','user_id','name','company','email','phone','website','address','source','status','is_favorite','follow_up_at'];
    protected $casts=['is_favorite'=>'boolean','follow_up_at'=>'datetime'];
    public function notes(){ return $this->hasMany(LeadNote::class); }
    public function tags(){ return $this->belongsToMany(Tag::class); }
}
