<?php
namespace Tests\Feature\Search;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class SearchTest extends TestCase {
  use RefreshDatabase;
  private function userWithTeam(): User { $user=User::factory()->create(); $team=Team::create(['owner_id'=>$user->id,'name'=>'HQ','slug'=>'hq']); $team->members()->attach($user->id,['role'=>'owner']); $user->forceFill(['active_team_id'=>$team->id])->save(); return $user; }
  public function test_user_can_search_for_leads(): void { $user=$this->userWithTeam(); $res=$this->actingAs($user,'sanctum')->postJson('/api/v1/search',['keyword'=>'restaurants','city'=>'Lagos']); $res->assertOk()->assertJsonPath('message','Search completed.')->assertJsonStructure(['results'=>[['name','phone','website','address','rating']]]); }
  public function test_search_requires_active_team(): void { $user=User::factory()->create(); $this->actingAs($user,'sanctum')->postJson('/api/v1/search',['keyword'=>'hotels','city'=>'Lagos'])->assertUnprocessable(); }
  public function test_history_is_team_scoped(): void { $user=$this->userWithTeam(); $this->actingAs($user,'sanctum')->postJson('/api/v1/search',['keyword'=>'salon','city'=>'Abuja']); $this->actingAs($user,'sanctum')->getJson('/api/v1/search/history')->assertOk()->assertJsonCount(1,'history'); }
}
