<?php

namespace App\Http\Controllers\Api\V1\Scoring;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadScoreHistory;
use App\Models\LeadScoreRule;
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
            'operator' => ['required', 'in:exists,equals'],
            'value' => ['nullable', 'string', 'max:255'],
            'points' => ['required', 'integer', 'min:-100', 'max:100'],
        ]);

        $rule = LeadScoreRule::create($data + [
            'team_id' => $teamId,
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Scoring rule created.',
            'rule' => $rule,
        ], 201);
    }

    public function calculate(Request $request, Lead $lead): JsonResponse
    {
        abort_unless($request->user()->active_team_id === $lead->team_id, 403);

        $rules = LeadScoreRule::where('team_id', $lead->team_id)
            ->where('is_active', true)
            ->get();

        $oldScore = (int) ($lead->score ?? 0);
        $oldTemperature = $lead->temperature ?? 'cold';
        $score = 0;
        $matched = [];

        foreach ($rules as $rule) {
            if ($this->matches($lead, $rule)) {
                $score += (int) $rule->points;
                $matched[] = ['rule_id' => $rule->id, 'name' => $rule->name, 'points' => $rule->points];
            }
        }

        $score = max(0, min(100, $score));
        $temperature = $this->temperature($score);

        $lead->update([
            'score' => $score,
            'temperature' => $temperature,
        ]);

        LeadScoreHistory::create([
            'team_id' => $lead->team_id,
            'lead_id' => $lead->id,
            'old_score' => $oldScore,
            'new_score' => $score,
            'old_temperature' => $oldTemperature,
            'new_temperature' => $temperature,
            'matched_rules' => $matched,
        ]);

        return response()->json([
            'message' => 'Lead score calculated.',
            'lead' => $lead->fresh(),
            'matched_rules' => $matched,
        ]);
    }

    public function history(Request $request, Lead $lead): JsonResponse
    {
        abort_unless($request->user()->active_team_id === $lead->team_id, 403);

        return response()->json([
            'history' => LeadScoreHistory::where('lead_id', $lead->id)->latest()->get(),
        ]);
    }

    private function matches(Lead $lead, LeadScoreRule $rule): bool
    {
        $value = $lead->{$rule->field} ?? null;

        if ($rule->operator === 'exists') {
            return ! empty($value);
        }

        if ($rule->operator === 'equals') {
            return (string) $value === (string) $rule->value;
        }

        return false;
    }

    private function temperature(int $score): string
    {
        if ($score >= 70) {
            return 'hot';
        }

        if ($score >= 35) {
            return 'warm';
        }

        return 'cold';
    }
}
