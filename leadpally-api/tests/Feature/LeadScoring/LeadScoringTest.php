<?php

namespace Tests\Feature\LeadScoring;

use App\Models\Lead;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadScoringTest extends TestCase
{
    use RefreshDatabase;

    private function userWithTeam(): User
    {
        $user = User::factory()->create();
        $team = Team::create([
            'owner_id' => $user->id,
            'name' => 'HQ',
            'slug' => 'hq-scoring',
        ]);
        $team->members()->attach($user->id, ['role' => 'owner']);
        $user->forceFill(['active_team_id' => $team->id])->save();

        return $user;
    }

    public function test_user_can_create_score_rule(): void
    {
        $user = $this->userWithTeam();

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/lead-scoring/rules', [
            'name' => 'Has phone',
            'field' => 'phone',
            'operator' => 'exists',
            'points' => 30,
        ])->assertCreated()->assertJsonPath('rule.name', 'Has phone');

        $this->assertDatabaseHas('lead_score_rules', [
            'team_id' => $user->active_team_id,
            'name' => 'Has phone',
            'points' => 30,
        ]);
    }

    public function test_lead_score_can_be_recalculated(): void
    {
        $user = $this->userWithTeam();

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/lead-scoring/rules', [
            'name' => 'Has phone',
            'field' => 'phone',
            'operator' => 'exists',
            'points' => 30,
        ])->assertCreated();

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/lead-scoring/rules', [
            'name' => 'Has website',
            'field' => 'website',
            'operator' => 'exists',
            'points' => 45,
        ])->assertCreated();

        $lead = Lead::create([
            'team_id' => $user->active_team_id,
            'user_id' => $user->id,
            'name' => 'Ada Stores',
            'phone' => '+2348012345678',
            'website' => 'https://example.com',
        ]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/leads/'.$lead->id.'/recalculate-score')
            ->assertOk()
            ->assertJsonPath('lead.score', 75)
            ->assertJsonPath('lead.temperature', 'hot');

        $this->assertDatabaseHas('lead_score_histories', [
            'lead_id' => $lead->id,
            'new_score' => 75,
            'new_temperature' => 'hot',
        ]);
    }

    public function test_lead_score_history_can_be_listed(): void
    {
        $user = $this->userWithTeam();
        $lead = Lead::create([
            'team_id' => $user->active_team_id,
            'user_id' => $user->id,
            'name' => 'Ada Stores',
        ]);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/leads/'.$lead->id.'/score-history')
            ->assertOk()
            ->assertJsonStructure(['history']);
    }
}
