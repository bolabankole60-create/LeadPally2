<?php

namespace App\Http\Controllers\Api\V1\LeadScoring;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadScoreRule;
use App\Services\LeadScoring\LeadScoringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeadScoringController extends Controller
{
    public function rules(Request $request): JsonResponse
    {
        $teamId = $request->user()->active_team_id;
        abort_unless($teamId, 422, 'Please create or switch to a team first.');

        return response()->json([
            'rules' => LeadScoreRule::where('team_id', $teamId)->latest()->get(),
        ]);
    }

    public function createRule(Request $request): JsonResponse
    {
        $teamId = $request->user()->active_team_id;
        abort_unless($teamId, 422, 'Please create or switch to a team first.');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'field' => ['required', 'in:email,phone,website,status,source'],
            'operator' => ['required', 'in:exists,equals,contains'],
            'value' => ['nullable', 'string', 'max:255'],
            'points' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $rule = LeadScoreRule::create($data + [
            'team_id' => $teamId,
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Score rule created.',
            'rule' => $rule,
        ], 201);
    }

    public function recalculate(Request $request, Lead $lead, LeadScoringService $service): JsonResponse
    {
        abort_unless($request->user()->active_team_id === $lead->team_id, 403);

        $lead = $service->recalculate($lead);

        return response()->json([
            'message' => 'Lead score recalculated.',
            'lead' => $lead,
        ]);
    }

    public function history(Request $request, Lead $lead): JsonResponse
    {
        abort_unless($request->user()->active_team_id === $lead->team_id, 403);

        return response()->json([
            'history' => $lead->scoreHistories()->latest()->get(),
        ]);
    }
}
