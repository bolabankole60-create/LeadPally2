<?php

namespace App\Http\Controllers\Api\V1\Ai;

use App\Http\Controllers\Controller;
use App\Models\AiConversation;
use App\Models\Lead;
use App\Services\Ai\SalesAssistantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiAssistantController extends Controller
{
    public function conversations(Request $request): JsonResponse
    {
        $teamId = $request->user()->active_team_id;
        abort_unless($teamId, 422, 'Please create or switch to a team first.');

        return response()->json([
            'conversations' => AiConversation::with('lead')
                ->where('team_id', $teamId)
                ->latest()
                ->get(),
        ]);
    }

    public function createConversation(Request $request): JsonResponse
    {
        $teamId = $request->user()->active_team_id;
        abort_unless($teamId, 422, 'Please create or switch to a team first.');

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'lead_id' => ['nullable', 'integer'],
            'purpose' => ['nullable', 'string', 'max:255'],
        ]);

        if (! empty($data['lead_id'])) {
            Lead::where('team_id', $teamId)->findOrFail($data['lead_id']);
        }

        $conversation = AiConversation::create([
            'team_id' => $teamId,
            'user_id' => $request->user()->id,
            'lead_id' => $data['lead_id'] ?? null,
            'title' => $data['title'] ?? 'AI Conversation',
            'purpose' => $data['purpose'] ?? 'sales_assistant',
        ]);

        return response()->json([
            'message' => 'Conversation created.',
            'conversation' => $conversation,
        ], 201);
    }

    public function show(Request $request, AiConversation $conversation): JsonResponse
    {
        abort_unless($request->user()->active_team_id === $conversation->team_id, 403);

        return response()->json([
            'conversation' => $conversation->load(['lead', 'messages']),
        ]);
    }

    public function chat(Request $request, AiConversation $conversation, SalesAssistantService $assistant): JsonResponse
    {
        abort_unless($request->user()->active_team_id === $conversation->team_id, 403);

        $data = $request->validate([
            'message' => ['required', 'string'],
            'tool' => ['nullable', 'in:assistant,email,whatsapp,call_script,lead_insight,next_action,meeting_summary,follow_up'],
        ]);

        $conversation->messages()->create([
            'role' => 'user',
            'content' => $data['message'],
        ]);

        $reply = $assistant->respond(
            $data['message'],
            $conversation->lead,
            $data['tool'] ?? 'assistant'
        );

        $assistantMessage = $conversation->messages()->create([
            'role' => 'assistant',
            'content' => $reply,
            'metadata' => ['tool' => $data['tool'] ?? 'assistant'],
        ]);

        return response()->json([
            'message' => $assistantMessage,
        ], 201);
    }

    public function tools(): JsonResponse
    {
        return response()->json([
            'tools' => [
                'assistant',
                'email',
                'whatsapp',
                'call_script',
                'lead_insight',
                'next_action',
                'meeting_summary',
                'follow_up',
            ],
        ]);
    }
}
