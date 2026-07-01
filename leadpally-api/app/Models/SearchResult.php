<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SearchResult extends Model {
  protected $fillable=['search_history_id','team_id','provider','provider_id','name','phone','website','address','rating','reviews_count'];
  protected $casts=['rating'=>'decimal:2'];
}
