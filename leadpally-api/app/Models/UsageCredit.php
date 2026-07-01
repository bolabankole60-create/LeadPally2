<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class UsageCredit extends Model {
  protected $fillable=['team_id','action','used','period_date'];
  protected $casts=['period_date'=>'date'];
}
