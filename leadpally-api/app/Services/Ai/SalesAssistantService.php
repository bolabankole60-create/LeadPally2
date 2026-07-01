<?php

namespace App\Services\Ai;

use App\Models\Lead;

class SalesAssistantService
{
    public function respond(string $message, ?Lead $lead = null, string $tool = 'assistant'): string
    {
        $context = $lead ? "Lead: {$lead->name}. Company: {$lead->company}. Status: {$lead->status}. Score: ".($lead->score ?? 0)."." : 'No lead context provided.';

        return match ($tool) {
            'email' => "Subject: Quick follow-up\n\nHello,\n\nThank you for your interest. I wanted to follow up and see how we can help you move forward.\n\n{$context}\n\nBest regards,",
            'whatsapp' => "Hi, this is a quick follow-up. I wanted to check if you are still interested and how we can help. {$context}",
            'call_script' => "Call script:\n1. Greet the lead warmly.\n2. Confirm their business need.\n3. Ask what challenge they want solved first.\n4. Present the next step clearly.\n5. Agree on a follow-up time.\n\n{$context}",
            'lead_insight' => "Lead insight: {$context} Review contact completeness, recent activity, score, and current status before deciding the next action.",
            'next_action' => "Next best action: follow up with a short personalized message, confirm interest, and schedule the next conversation.",
            'meeting_summary' => "Meeting summary draft: Key discussion points were captured. Next steps should include owner, deadline, and follow-up action. User note: {$message}",
            'follow_up' => "Follow-up suggestion: send a friendly reminder today and offer one clear next step. Keep the message short and easy to reply to.",
            default => "AI assistant response: {$message}\n\n{$context}\n\nSuggested next step: qualify the lead, confirm interest, and schedule a follow-up.",
        };
    }
}
