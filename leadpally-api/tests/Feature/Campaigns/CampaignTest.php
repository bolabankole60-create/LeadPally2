<?php

namespace Tests\Feature\Campaigns;

use App\Models\Campaign;
use App\Models\Lead;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignTest extends TestCase
{
    use RefreshDatabase;

    private function userWithTeam(): User
    {
        $user = User::factory()->create();
        $team = Team::create([
            'owner_id' => $user->id,
            'name' => 'HQ',
            'slug' => 'hq-campaigns',
        ]);
        $team->members()->attach($user->id, ['role' => 'owner']);
        $user->forceFill(['active_team_id' => $team->id])->save();

        return $user;
    }

    public function test_user_can_create_campaign(): void
    {
        $user = $this->userWithTeam();

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/campaigns', [
            'name' => 'Intro Campaign',
            'channel' => 'email',
            'subject' => 'Hello',
            'body' => 'Welcome to LeadPally',
        ])->assertCreated()->assertJsonPath('campaign.name', 'Intro Campaign');

        $this->assertDatabaseHas('campaigns', [
            'team_id' => $user->active_team_id,
            'name' => 'Intro Campaign',
            'status' => 'draft',
        ]);
    }

    public function test_user_can_add_audience_to_campaign(): void
    {
        $user = $this->userWithTeam();
        $lead = Lead::create([
            'team_id' => $user->active_team_id,
            'user_id' => $user->id,
            'name' => 'Ada Stores',
        ]);
        $campaign = Campaign::create([
            'team_id' => $user->active_team_id,
            'user_id' => $user->id,
            'name' => 'Intro Campaign',
            'channel' => 'email',
            'status' => 'draft',
            'body' => 'Hello',
        ]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/campaigns/'.$campaign->id.'/audience', [
                'lead_ids' => [$lead->id],
            ])
            ->assertOk()
            ->assertJsonPath('count', 1);

        $this->assertDatabaseHas('campaign_audiences', [
            'campaign_id' => $campaign->id,
            'lead_id' => $lead->id,
            'status' => 'pending',
        ]);
    }

    public function test_user_can_schedule_start_and_complete_campaign(): void
    {
        $user = $this->userWithTeam();
        $campaign = Campaign::create([
            'team_id' => $user->active_team_id,
            'user_id' => $user->id,
            'name' => 'Intro Campaign',
            'channel' => 'email',
            'status' => 'draft',
            'body' => 'Hello',
        ]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/campaigns/'.$campaign->id.'/schedule', [
                'scheduled_at' => now()->addDay()->toIso8601String(),
            ])
            ->assertOk()
            ->assertJsonPath('campaign.status', 'scheduled');

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/campaigns/'.$campaign->id.'/start')
            ->assertOk()
            ->assertJsonPath('campaign.status', 'running');

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/campaigns/'.$campaign->id.'/complete')
            ->assertOk()
            ->assertJsonPath('campaign.status', 'completed');
    }
}
