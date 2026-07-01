<?php
namespace App\Http\Controllers\Api\V1\Teams;
use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TeamController extends Controller {
    public function index(Request $request): JsonResponse { return response()->json(['teams'=>$request->user()->teams()->get(),'active_team_id'=>$request->user()->active_team_id]); }
    public function store(Request $request): JsonResponse {
        $data=$request->validate(['name'=>['required','string','max:255']]); $user=$request->user();
        $team=DB::transaction(function() use($data,$user){ $team=Team::create(['owner_id'=>$user->id,'name'=>$data['name'],'slug'=>Str::slug($data['name']).'-'.Str::lower(Str::random(6))]); $team->members()->attach($user->id,['role'=>'owner']); $user->forceFill(['active_team_id'=>$team->id])->save(); return $team; });
        return response()->json(['message'=>'Team created.','team'=>$team],201);
    }
    public function switch(Request $request, Team $team): JsonResponse { abort_unless($request->user()->teams()->where('teams.id',$team->id)->exists(),403); $request->user()->forceFill(['active_team_id'=>$team->id])->save(); return response()->json(['message'=>'Active team switched.','team'=>$team]); }
    public function update(Request $request, Team $team): JsonResponse { $this->authorizeOwner($request->user(),$team); $data=$request->validate(['name'=>['required','string','max:255']]); $team->update(['name'=>$data['name']]); return response()->json(['message'=>'Team updated.','team'=>$team]); }
    public function invite(Request $request, Team $team): JsonResponse { $this->authorizeOwner($request->user(),$team); $data=$request->validate(['email'=>['required','email'],'role'=>['required','in:admin,member']]); $inv=TeamInvitation::create(['team_id'=>$team->id,'invited_by'=>$request->user()->id,'email'=>$data['email'],'role'=>$data['role'],'token'=>Str::random(40),'expires_at'=>now()->addDays(7)]); return response()->json(['message'=>'Invitation created.','invitation'=>$inv],201); }
    public function accept(Request $request, string $token): JsonResponse { $inv=TeamInvitation::where('token',$token)->whereNull('accepted_at')->firstOrFail(); abort_if($inv->expires_at && $inv->expires_at->isPast(),410); abort_unless(strtolower($request->user()->email)===strtolower($inv->email),403); $inv->team->members()->syncWithoutDetaching([$request->user()->id=>['role'=>$inv->role]]); $request->user()->forceFill(['active_team_id'=>$inv->team_id])->save(); $inv->update(['accepted_at'=>now()]); return response()->json(['message'=>'Invitation accepted.','team'=>$inv->team]); }
    public function members(Request $request, Team $team): JsonResponse { abort_unless($request->user()->teams()->where('teams.id',$team->id)->exists(),403); return response()->json(['members'=>$team->members()->get()]); }
    private function authorizeOwner(User $user, Team $team): void { abort_unless($team->members()->where('users.id',$user->id)->wherePivot('role','owner')->exists(),403); }
}
