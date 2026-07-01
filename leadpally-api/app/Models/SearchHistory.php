<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SearchHistory extends Model {
  protected $fillable=['team_id','user_id','keyword','city','country','results_count'];
  public function results(){ return $this->hasMany(SearchResult::class); }
}
