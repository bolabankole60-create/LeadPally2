<?php

namespace Tests\Feature\Workflows;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function userWithTeam(): User
    {
        $user = User::factory()->create();
        $team = Team::create([
            'owner_id' => $user->id,
            'name' => 'HQ',
            'slug' => 'hq-workflows',
        ]);
        $team->members()->attach($user->id, ['role' => 'owner']);
        $user->forceFill(['active_team_id' => $team->id])->save();

        return $user;
    }

    public function test_user_can_create_workflow(): void
    {
        $user = $this->userWithTeam();

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/workflows', [
            'name' => 'Notify on lead created',
            'event_name' => 'lead_created',
            'steps' => [
                ['type' => 'notification', 'title' => 'New lead', 'body' => 'A new lead was created.'],
            ],
        ])->assertCreated()->assertJsonPath('workflow.name', 'Notify on lead created');

        $this->assertDatabaseHas('workflow_rules', [
            'team_id' => $user->active_team_id,
            'event_name' => 'lead_created',
            'is_active' => true,
        ]);
    }

    public function test_workflow_event_can_be_triggered(): void
    {
        $user = $this->userWithTeam();

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/workflows', [
            'name' => 'Notify on lead created',
            'event_name' => 'lead_created',
            'steps' => [
                ['type' => 'notification', 'title' => 'New lead', 'body' => 'A new lead was created.'],
            ],
        ])->assertCreated();

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/workflows/trigger', [
            'event_name' => 'lead_created',
            'payload' => ['lead_id' => 1],
        ])->assertOk()->assertJsonPath('matched', 1);

        $this->assertDatabaseHas('workflow_runs', [
            'team_id' => $user->active_team_id,
            'event_name' => 'lead_created',
            'status' => 'success',
        ]);

        $this->assertDatabaseHas('notifications', [
            'team_id' => $user->active_team_id,
            'type' => 'workflow',
            'title' => 'New lead',
        ]);
    }

    public function test_workflow_runs_can_be_listed(): void
    {
        $user = $this->userWithTeam();

        $this->actingAs($user, 'sanctum')->getJson('/api/v1/workflow-runs')
            ->assertOk()
            ->assertJsonStructure(['runs']);
    }
}
