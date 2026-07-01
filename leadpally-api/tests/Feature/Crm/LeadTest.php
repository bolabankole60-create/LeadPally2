<?php
namespace Tests\Feature\Crm;
use App\Models\Lead;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class LeadTest extends TestCase {
    use RefreshDatabase;
    private function userWithTeam(): User { $user=User::factory()->create(); $team=Team::create(['owner_id'=>$user->id,'name'=>'HQ','slug'=>'hq-crm']); $team->members()->attach($user->id,['role'=>'owner']); $user->forceFill(['active_team_id'=>$team->id])->save(); return $user; }
    public function test_user_can_save_lead(): void { $user=$this->userWithTeam(); $res=$this->actingAs($user,'sanctum')->postJson('/api/v1/leads',['name'=>'Ada Stores','phone'=>'+2348012345678']); $res->assertCreated()->assertJsonPath('lead.name','Ada Stores'); $this->assertDatabaseHas('leads',['team_id'=>$user->active_team_id,'name'=>'Ada Stores']); }
    public function test_user_can_update_lead_status_and_favorite(): void { $user=$this->userWithTeam(); $lead=Lead::create(['team_id'=>$user->active_team_id,'user_id'=>$user->id,'name'=>'Lead A']); $res=$this->actingAs($user,'sanctum')->putJson('/api/v1/leads/'.$lead->id,['status'=>'contacted','is_favorite'=>true]); $res->assertOk()->assertJsonPath('lead.status','contacted')->assertJsonPath('lead.is_favorite',true); }
    public function test_user_can_add_note_and_tag(): void { $user=$this->userWithTeam(); $lead=Lead::create(['team_id'=>$user->active_team_id,'user_id'=>$user->id,'name'=>'Lead A']); $this->actingAs($user,'sanctum')->postJson('/api/v1/leads/'.$lead->id.'/notes',['body'=>'Called owner'])->assertCreated(); $this->actingAs($user,'sanctum')->postJson('/api/v1/leads/'.$lead->id.'/tags',['name'=>'Hot'])->assertCreated(); $this->assertDatabaseHas('lead_notes',['lead_id'=>$lead->id,'body'=>'Called owner']); $this->assertDatabaseHas('tags',['team_id'=>$user->active_team_id,'name'=>'Hot']); }
}
