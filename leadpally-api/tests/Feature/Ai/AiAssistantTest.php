<?php

namespace Tests\Feature\Ai;

use App\Models\Lead;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiAssistantTest extends TestCase
{
    use RefreshDatabase;

    private function userWithTeam(): User
    {
        $user = User::factory()->create();
        $team = Team::create([
            'owner_id' => $user->id,
            'name' => 'HQ',
            'slug' => 'hq-ai',
        ]);
        $team->members()->attach($user->id, ['role' => 'owner']);
        $user->forceFill(['active_team_id' => $team->id])->save();

        return $user;
    }

    public function test_user_can_create_ai_conversation(): void
    {
        $user = $this->userWithTeam();

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/ai/conversations', [
            'title' => 'Sales help',
            'purpose' => 'sales_assistant',
        ])->assertCreated()->assertJsonPath('conversation.title', 'Sales help');

        $this->assertDatabaseHas('ai_conversations', [
            'team_id' => $user->active_team_id,
            'title' => 'Sales help',
        ]);
    }

    public function test_user_can_chat_with_ai_assistant(): void
    {
        $user = $this->userWithTeam();
        $lead = Lead::create([
            'team_id' => $user->active_team_id,
            'user_id' => $user->id,
            'name' => 'Ada Stores',
            'status' => 'new',
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/ai/conversations', [
            'title' => 'Ada follow-up',
            'lead_id' => $lead->id,
        ])->assertCreated();

        $conversationId = $response->json('conversation.id');

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/ai/conversations/'.$conversationId.'/chat', [
            'message' => 'Draft a follow-up message',
            'tool' => 'whatsapp',
        ])->assertCreated()->assertJsonPath('message.role', 'assistant');

        $this->assertDatabaseCount('ai_messages', 2);
    }

    public function test_ai_tools_endpoint_returns_tools(): void
    {
        $user = $this->userWithTeam();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/ai/tools')
            ->assertOk()
            ->assertJsonStructure(['tools']);
    }
}
