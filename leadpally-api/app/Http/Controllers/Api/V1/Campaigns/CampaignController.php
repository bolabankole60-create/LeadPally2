<?php

namespace App\Http\Controllers\Api\V1\Campaigns;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $teamId = $request->user()->active_team_id;
        abort_unless($teamId, 422, 'Please create or switch to a team first.');

        return response()->json([
            'campaigns' => Campaign::withCount('audiences')
                ->where('team_id', $teamId)
                ->latest()
                ->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $teamId = $request->user()->active_team_id;
        abort_unless($teamId, 422, 'Please create or switch to a team first.');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'channel' => ['required', 'in:email,whatsapp,sms'],
            'subject' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'scheduled_at' => ['nullable', 'date'],
        ]);

        $campaign = Campaign::create($data + [
            'team_id' => $teamId,
            'user_id' => $request->user()->id,
            'status' => empty($data['scheduled_at']) ? 'draft' : 'scheduled',
        ]);

        return response()->json([
            'message' => 'Campaign created.',
            'campaign' => $campaign,
        ], 201);
    }

    public function show(Request $request, Campaign $campaign): JsonResponse
    {
        $this->guard($request, $campaign);

        return response()->json([
            'campaign' => $campaign->load(['audiences.lead']),
        ]);
    }

    public function addAudience(Request $request, Campaign $campaign): JsonResponse
    {
        $this->guard($request, $campaign);

        $data = $request->validate([
            'lead_ids' => ['required', 'array'],
            'lead_ids.*' => ['integer'],
        ]);

        $validLeadIds = Lead::where('team_id', $campaign->team_id)
            ->whereIn('id', $data['lead_ids'])
            ->pluck('id')
            ->all();

        foreach ($validLeadIds as $leadId) {
            $campaign->audiences()->firstOrCreate([
                'lead_id' => $leadId,
            ], [
                'status' => 'pending',
            ]);
        }

        return response()->json([
            'message' => 'Audience added.',
            'count' => count($validLeadIds),
        ]);
    }

    public function schedule(Request $request, Campaign $campaign): JsonResponse
    {
        $this->guard($request, $campaign);

        $data = $request->validate([
            'scheduled_at' => ['required', 'date'],
        ]);

        $campaign->update([
            'scheduled_at' => $data['scheduled_at'],
            'status' => 'scheduled',
        ]);

        return response()->json([
            'message' => 'Campaign scheduled.',
            'campaign' => $campaign->fresh(),
        ]);
    }

    public function start(Request $request, Campaign $campaign): JsonResponse
    {
        $this->guard($request, $campaign);

        $campaign->update([
            'status' => 'running',
            'started_at' => now(),
        ]);

        return response()->json([
            'message' => 'Campaign started.',
            'campaign' => $campaign->fresh(),
        ]);
    }

    public function complete(Request $request, Campaign $campaign): JsonResponse
    {
        $this->guard($request, $campaign);

        $campaign->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return response()->json([
            'message' => 'Campaign completed.',
            'campaign' => $campaign->fresh(),
        ]);
    }

    public function analytics(Request $request): JsonResponse
    {
        $teamId = $request->user()->active_team_id;
        abort_unless($teamId, 422, 'Please create or switch to a team first.');

        return response()->json([
            'total_campaigns' => Campaign::where('team_id', $teamId)->count(),
            'scheduled_campaigns' => Campaign::where('team_id', $teamId)->where('status', 'scheduled')->count(),
            'running_campaigns' => Campaign::where('team_id', $teamId)->where('status', 'running')->count(),
            'completed_campaigns' => Campaign::where('team_id', $teamId)->where('status', 'completed')->count(),
        ]);
    }

    private function guard(Request $request, Campaign $campaign): void
    {
        abort_unless($request->user()->active_team_id === $campaign->team_id, 403);
    }
}
