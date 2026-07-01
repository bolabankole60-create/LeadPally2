<?php

namespace App\Http\Controllers\Api\V1\Workflows;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\WorkflowRule;
use App\Models\WorkflowRun;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkflowController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $teamId = $request->user()->active_team_id;
        abort_unless($teamId, 422, 'Please create or switch to a team first.');

        return response()->json([
            'workflows' => WorkflowRule::where('team_id', $teamId)->latest()->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $teamId = $request->user()->active_team_id;
        abort_unless($teamId, 422, 'Please create or switch to a team first.');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'event_name' => ['required', 'in:lead_created,lead_updated,status_changed,deal_created,deal_won,deal_lost,campaign_completed'],
            'criteria' => ['nullable', 'array'],
            'steps' => ['required', 'array'],
        ]);

        $workflow = WorkflowRule::create($data + [
            'team_id' => $teamId,
            'user_id' => $request->user()->id,
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Workflow created.',
            'workflow' => $workflow,
        ], 201);
    }

    public function trigger(Request $request): JsonResponse
    {
        $teamId = $request->user()->active_team_id;
        abort_unless($teamId, 422, 'Please create or switch to a team first.');

        $data = $request->validate([
            'event_name' => ['required', 'string'],
            'payload' => ['nullable', 'array'],
        ]);

        $rules = WorkflowRule::where('team_id', $teamId)
            ->where('event_name', $data['event_name'])
            ->where('is_active', true)
            ->get();

        foreach ($rules as $rule) {
            $run = WorkflowRun::create([
                'workflow_rule_id' => $rule->id,
                'team_id' => $teamId,
                'event_name' => $data['event_name'],
                'status' => 'success',
                'payload' => $data['payload'] ?? [],
                'executed_at' => now(),
            ]);

            foreach ($rule->steps ?? [] as $step) {
                if (($step['type'] ?? null) === 'notification') {
                    AppNotification::create([
                        'team_id' => $teamId,
                        'user_id' => $request->user()->id,
                        'type' => 'workflow',
                        'title' => $step['title'] ?? 'Workflow action executed',
                        'body' => $step['body'] ?? 'A workflow action has been completed.',
                    ]);
                }
            }
        }

        return response()->json([
            'message' => 'Workflow event processed.',
            'matched' => $rules->count(),
        ]);
    }

    public function runs(Request $request): JsonResponse
    {
        $teamId = $request->user()->active_team_id;
        abort_unless($teamId, 422, 'Please create or switch to a team first.');

        return response()->json([
            'runs' => WorkflowRun::where('team_id', $teamId)->latest()->limit(100)->get(),
        ]);
    }
}
