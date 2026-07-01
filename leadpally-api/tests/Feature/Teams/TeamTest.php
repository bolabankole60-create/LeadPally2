<?php
namespace Tests\Feature\Teams;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class TeamTest extends TestCase {
    use RefreshDatabase;
    public function test_user_can_create_team(): void { $user=User::factory()->create(); $res=$this->actingAs($user,'sanctum')->postJson('/api/v1/teams',['name'=>'LeadPally HQ']); $res->assertCreated()->assertJsonPath('team.name','LeadPally HQ'); $this->assertDatabaseHas('team_user',['user_id'=>$user->id,'role'=>'owner']); $this->assertNotNull($user->fresh()->active_team_id); }
    public function test_user_can_switch_team(): void { $user=User::factory()->create(); $team=Team::create(['owner_id'=>$user->id,'name'=>'Team A','slug'=>'team-a']); $team->members()->attach($user->id,['role'=>'owner']); $res=$this->actingAs($user,'sanctum')->postJson("/api/v1/teams/{$team->id}/switch"); $res->assertOk(); $this->assertSame($team->id,$user->fresh()->active_team_id); }
    public function test_owner_can_invite_member(): void { $owner=User::factory()->create(); $team=Team::create(['owner_id'=>$owner->id,'name'=>'Team A','slug'=>'team-a']); $team->members()->attach($owner->id,['role'=>'owner']); $res=$this->actingAs($owner,'sanctum')->postJson("/api/v1/teams/{$team->id}/invite",['email'=>'member@example.com','role'=>'member']); $res->assertCreated(); $this->assertDatabaseHas('team_invitations',['email'=>'member@example.com']); }
    public function test_invited_user_can_accept_invitation(): void { $owner=User::factory()->create(); $member=User::factory()->create(['email'=>'member@example.com']); $team=Team::create(['owner_id'=>$owner->id,'name'=>'Team A','slug'=>'team-a']); $team->members()->attach($owner->id,['role'=>'owner']); $inv=TeamInvitation::create(['team_id'=>$team->id,'invited_by'=>$owner->id,'email'=>'member@example.com','role'=>'member','token'=>'abc123','expires_at'=>now()->addDay()]); $res=$this->actingAs($member,'sanctum')->postJson('/api/v1/team-invitations/abc123/accept'); $res->assertOk(); $this->assertDatabaseHas('team_user',['team_id'=>$team->id,'user_id'=>$member->id,'role'=>'member']); $this->assertNotNull($inv->fresh()->accepted_at); }
}
