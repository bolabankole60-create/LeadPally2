<?php

namespace Tests\Feature\Reports;

use App\Models\Campaign;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    private function userWithTeam(): User
    {
        $user = User::factory()->create();
        $team = Team::create([
            'owner_id' => $user->id,
            'name' => 'HQ',
            'slug' => 'hq-reports',
        ]);
        $team->members()->attach($user->id, ['role' => 'owner']);
        $user->forceFill(['active_team_id' => $team->id])->save();
        return $user;
    }

    public function test_dashboard_report_returns_kpis(): void
    {
        $user = $this->userWithTeam();
        Lead::create(['team_id' => $user->active_team_id, 'user_id' => $user->id, 'name' => 'Ada', 'status' => 'new']);
        $pipeline = Pipeline::create(['team_id' => $user->active_team_id, 'name' => 'Sales']);
        $stage = PipelineStage::create(['pipeline_id' => $pipeline->id, 'name' => 'New', 'position' => 1]);
        Deal::create(['team_id' => $user->active_team_id, 'pipeline_id' => $pipeline->id, 'pipeline_stage_id' => $stage->id, 'title' => 'Deal', 'value' => 50000, 'status' => 'open']);
        Campaign::create(['team_id' => $user->active_team_id, 'user_id' => $user->id, 'name' => 'Intro', 'channel' => 'email', 'status' => 'draft', 'body' => 'Hello']);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/reports/dashboard')
            ->assertOk()
            ->assertJsonPath('kpis.total_leads', 1)
            ->assertJsonPath('kpis.open_deals', 1)
            ->assertJsonPath('kpis.campaigns', 1);
    }

    public function test_revenue_report_returns_values(): void
    {
        $user = $this->userWithTeam();
        $pipeline = Pipeline::create(['team_id' => $user->active_team_id, 'name' => 'Sales']);
        $stage = PipelineStage::create(['pipeline_id' => $pipeline->id, 'name' => 'New', 'position' => 1]);
        Deal::create(['team_id' => $user->active_team_id, 'pipeline_id' => $pipeline->id, 'pipeline_stage_id' => $stage->id, 'title' => 'Deal', 'value' => 25000, 'status' => 'won']);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/reports/revenue')
            ->assertOk()
            ->assertJsonPath('won_value', '25000.00');
    }

    public function test_report_export_returns_csv(): void
    {
        $user = $this->userWithTeam();

        $this->actingAs($user, 'sanctum')
            ->get('/api/v1/reports/export')
            ->assertOk();
    }
}
