<?php
namespace App\Http\Controllers\Api\V1\Search;
use App\Http\Controllers\Controller;
use App\Models\SearchHistory;
use App\Models\SearchResult;
use App\Models\UsageCredit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class SearchController extends Controller {
  public function search(Request $request): JsonResponse {
    $data=$request->validate(['keyword'=>['required','string','max:255'],'city'=>['required','string','max:255']]);
    $user=$request->user(); abort_unless($user->active_team_id,422,'Please create or switch to a team first.');
    $credit=UsageCredit::firstOrCreate(['team_id'=>$user->active_team_id,'action'=>'search','period_date'=>now()->toDateString()],['used'=>0]);
    abort_if($credit->used>=5,429,'Daily search limit reached.');
    $results=[['provider_id'=>'demo-1','name'=>ucwords($data['keyword']).' Hub '.$data['city'],'phone'=>'+2348012345678','website'=>'https://example.com','address'=>'1 Market Road, '.$data['city'],'rating'=>4.5,'reviews_count'=>120]];
    $history=DB::transaction(function() use($user,$data,$results,$credit){
      $history=SearchHistory::create(['team_id'=>$user->active_team_id,'user_id'=>$user->id,'keyword'=>$data['keyword'],'city'=>$data['city'],'country'=>'Nigeria','results_count'=>count($results)]);
      foreach($results as $r){ SearchResult::create(['search_history_id'=>$history->id,'team_id'=>$user->active_team_id,'provider'=>'google_places']+$r); }
      $credit->increment('used'); return $history->load('results');
    });
    return response()->json(['message'=>'Search completed.','search'=>$history,'results'=>$history->results]);
  }
  public function history(Request $request): JsonResponse {
    $user=$request->user(); abort_unless($user->active_team_id,422);
    return response()->json(['history'=>SearchHistory::where('team_id',$user->active_team_id)->latest()->get()]);
  }
}
