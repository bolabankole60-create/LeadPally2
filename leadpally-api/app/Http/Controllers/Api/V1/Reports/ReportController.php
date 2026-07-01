<?php

namespace App\Http\Controllers\Api\V1\Reports;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function dashboard(Request $request): JsonResponse
    {
        $teamId = $this->teamId($request);

        return response()->json([
            'kpis' => [
                'total_leads' => Lead::where('team_id', $teamId)->count(),
                'hot_leads' => Lead::where('team_id', $teamId)->where('temperature', 'hot')->count(),
                'open_deals' => Deal::where('team_id', $teamId)->where('status', 'open')->count(),
                'won_deals' => Deal::where('team_id', $teamId)->where('status', 'won')->count(),
                'pipeline_value' => Deal::where('team_id', $teamId)->where('status', 'open')->sum('value'),
                'won_value' => Deal::where('team_id', $teamId)->where('status', 'won')->sum('value'),
                'campaigns' => Campaign::where('team_id', $teamId)->count(),
            ],
            'lead_status' => $this->leadStatus($teamId),
            'deal_status' => $this->dealStatus($teamId),
            'campaign_status' => $this->campaignStatus($teamId),
        ]);
    }

    public function leads(Request $request): JsonResponse
    {
        $teamId = $this->teamId($request);

        return response()->json([
            'total' => Lead::where('team_id', $teamId)->count(),
            'by_status' => $this->leadStatus($teamId),
            'by_temperature' => Lead::where('team_id', $teamId)
                ->selectRaw('temperature, count(*) as total')
                ->groupBy('temperature')
                ->get(),
        ]);
    }

    public function revenue(Request $request): JsonResponse
    {
        $teamId = $this->teamId($request);

        return response()->json([
            'open_value' => Deal::where('team_id', $teamId)->where('status', 'open')->sum('value'),
            'won_value' => Deal::where('team_id', $teamId)->where('status', 'won')->sum('value'),
            'lost_value' => Deal::where('team_id', $teamId)->where('status', 'lost')->sum('value'),
        ]);
    }

    public function team(Request $request): JsonResponse
    {
        $teamId = $this->teamId($request);
        $team = Team::withCount('members')->findOrFail($teamId);

        return response()->json([
            'team' => $team,
            'members' => $team->members_count,
            'leads' => Lead::where('team_id', $teamId)->count(),
            'deals' => Deal::where('team_id', $teamId)->count(),
        ]);
    }

    public function campaigns(Request $request): JsonResponse
    {
        $teamId = $this->teamId($request);

        return response()->json([
            'total' => Campaign::where('team_id', $teamId)->count(),
            'by_status' => $this->campaignStatus($teamId),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $teamId = $this->teamId($request);
        $filename = 'leadpally-report.csv';

        return response()->streamDownload(function () use ($teamId) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Metric', 'Value']);
            fputcsv($handle, ['Total Leads', Lead::where('team_id', $teamId)->count()]);
            fputcsv($handle, ['Open Deals', Deal::where('team_id', $teamId)->where('status', 'open')->count()]);
            fputcsv($handle, ['Won Deals', Deal::where('team_id', $teamId)->where('status', 'won')->count()]);
            fputcsv($handle, ['Pipeline Value', Deal::where('team_id', $teamId)->where('status', 'open')->sum('value')]);
            fputcsv($handle, ['Won Value', Deal::where('team_id', $teamId)->where('status', 'won')->sum('value')]);
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function teamId(Request $request): int
    {
        $teamId = $request->user()->active_team_id;
        abort_unless($teamId, 422, 'Please create or switch to a team first.');
        return (int) $teamId;
    }

    private function leadStatus(int $teamId)
    {
        return Lead::where('team_id', $teamId)->selectRaw('status, count(*) as total')->groupBy('status')->get();
    }

    private function dealStatus(int $teamId)
    {
        return Deal::where('team_id', $teamId)->selectRaw('status, count(*) as total')->groupBy('status')->get();
    }

    private function campaignStatus(int $teamId)
    {
        return Campaign::where('team_id', $teamId)->selectRaw('status, count(*) as total')->groupBy('status')->get();
    }
}
