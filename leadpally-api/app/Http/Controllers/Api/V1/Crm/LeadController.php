<?php
namespace App\Http\Controllers\Api\V1\Crm;
use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeadController extends Controller {
    public function index(Request $request): JsonResponse {
        $teamId=$request->user()->active_team_id; abort_unless($teamId,422,'Please create or switch to a team first.');
        $leads=Lead::with(['notes','tags'])->where('team_id',$teamId)->latest()->get();
        return response()->json(['leads'=>$leads]);
    }
    public function store(Request $request): JsonResponse {
        $teamId=$request->user()->active_team_id; abort_unless($teamId,422,'Please create or switch to a team first.');
        $data=$request->validate(['name'=>['required','string','max:255'],'company'=>['nullable','string','max:255'],'email'=>['nullable','email'],'phone'=>['nullable','string','max:50'],'website'=>['nullable','string','max:255'],'address'=>['nullable','string'],'source'=>['nullable','string','max:50']]);
        $lead=Lead::create($data+['team_id'=>$teamId,'user_id'=>$request->user()->id,'source'=>$data['source'] ?? 'manual']);
        return response()->json(['message'=>'Lead saved.','lead'=>$lead],201);
    }
    public function show(Request $request, Lead $lead): JsonResponse { $this->guard($request,$lead); return response()->json(['lead'=>$lead->load(['notes','tags'])]); }
    public function update(Request $request, Lead $lead): JsonResponse {
        $this->guard($request,$lead);
        $data=$request->validate(['name'=>['sometimes','string','max:255'],'status'=>['sometimes','in:new,contacted,qualified,won,lost'],'is_favorite'=>['sometimes','boolean'],'follow_up_at'=>['nullable','date']]);
        $lead->update($data); return response()->json(['message'=>'Lead updated.','lead'=>$lead->fresh()]);
    }
    public function note(Request $request, Lead $lead): JsonResponse { $this->guard($request,$lead); $data=$request->validate(['body'=>['required','string']]); $note=$lead->notes()->create(['user_id'=>$request->user()->id,'body'=>$data['body']]); return response()->json(['message'=>'Note added.','note'=>$note],201); }
    public function tag(Request $request, Lead $lead): JsonResponse { $this->guard($request,$lead); $data=$request->validate(['name'=>['required','string','max:50']]); $tag=Tag::firstOrCreate(['team_id'=>$lead->team_id,'name'=>$data['name']]); $lead->tags()->syncWithoutDetaching([$tag->id]); return response()->json(['message'=>'Tag added.','tag'=>$tag],201); }
    private function guard(Request $request, Lead $lead): void { abort_unless($request->user()->active_team_id === $lead->team_id,403); }
}
