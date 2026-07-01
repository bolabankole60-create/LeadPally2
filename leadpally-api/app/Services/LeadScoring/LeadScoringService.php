<?php

namespace App\Services\LeadScoring;

use App\Models\Lead;
use App\Models\LeadScoreHistory;
use App\Models\LeadScoreRule;

class LeadScoringService
{
    public function recalculate(Lead $lead): Lead
    {
        $oldScore = $lead->score ?? 0;
        $oldTemperature = $lead->temperature ?? 'cold';
        $matchedRules = [];
        $score = 0;

        $rules = LeadScoreRule::where('team_id', $lead->team_id)
            ->where('is_active', true)
            ->get();

        foreach ($rules as $rule) {
            if ($this->matches($lead, $rule)) {
                $score += max(0, (int) $rule->points);
                $matchedRules[] = $rule->name;
            }
        }

        $temperature = $this->temperature($score);

        $lead->update([
            'score' => $score,
            'temperature' => $temperature,
        ]);

        LeadScoreHistory::create([
            'lead_id' => $lead->id,
            'team_id' => $lead->team_id,
            'old_score' => $oldScore,
            'new_score' => $score,
            'old_temperature' => $oldTemperature,
            'new_temperature' => $temperature,
            'matched_rules' => $matchedRules,
        ]);

        return $lead->fresh();
    }

    private function matches(Lead $lead, LeadScoreRule $rule): bool
    {
        $value = $lead->{$rule->field} ?? null;

        return match ($rule->operator) {
            'exists' => !empty($value),
            'equals' => (string) $value === (string) $rule->value,
            'contains' => str_contains(strtolower((string) $value), strtolower((string) $rule->value)),
            default => false,
        };
    }

    private function temperature(int $score): string
    {
        if ($score >= 70) {
            return 'hot';
        }

        if ($score >= 40) {
            return 'warm';
        }

        return 'cold';
    }
}
