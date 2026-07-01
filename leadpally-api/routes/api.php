<?php

use App\Http\Controllers\Api\V1\Ai\AiAssistantController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Campaigns\CampaignController;
use App\Http\Controllers\Api\V1\Crm\LeadController;
use App\Http\Controllers\Api\V1\Scoring\LeadScoringController;
use App\Http\Controllers\Api\V1\Search\SearchController;
use App\Http\Controllers\Api\V1\Teams\TeamController;
use App\Http\Controllers\Api\V1\Workflows\WorkflowController;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => response()->json([
    'status' => 'healthy',
    'timestamp' => now()->toIso8601String(),
]));

Route::prefix('v1')->group(function () {
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        Route::get('/teams', [TeamController::class, 'index']);
        Route::post('/teams', [TeamController::class, 'store']);
        Route::post('/teams/{team}/switch', [TeamController::class, 'switch']);
        Route::put('/teams/{team}', [TeamController::class, 'update']);
        Route::post('/teams/{team}/invite', [TeamController::class, 'invite']);
        Route::post('/team-invitations/{token}/accept', [TeamController::class, 'accept']);
        Route::get('/teams/{team}/members', [TeamController::class, 'members']);

        Route::post('/search', [SearchController::class, 'search']);
        Route::get('/search/history', [SearchController::class, 'history']);

        Route::get('/lead-scoring/rules', [LeadScoringController::class, 'rules']);
        Route::post('/lead-scoring/rules', [LeadScoringController::class, 'createRule']);
        Route::post('/leads/{lead}/calculate-score', [LeadScoringController::class, 'calculate']);
        Route::post('/leads/{lead}/recalculate-score', [LeadScoringController::class, 'calculate']);
        Route::get('/leads/{lead}/score-history', [LeadScoringController::class, 'history']);

        Route::get('/leads', [LeadController::class, 'index']);
        Route::post('/leads', [LeadController::class, 'store']);
        Route::get('/leads/{lead}', [LeadController::class, 'show']);
        Route::put('/leads/{lead}', [LeadController::class, 'update']);
        Route::post('/leads/{lead}/notes', [LeadController::class, 'note']);
        Route::post('/leads/{lead}/tags', [LeadController::class, 'tag']);

        Route::get('/campaigns', [CampaignController::class, 'index']);
        Route::post('/campaigns', [CampaignController::class, 'store']);
        Route::get('/campaigns/analytics', [CampaignController::class, 'analytics']);
        Route::get('/campaigns/{campaign}', [CampaignController::class, 'show']);
        Route::post('/campaigns/{campaign}/audience', [CampaignController::class, 'addAudience']);
        Route::post('/campaigns/{campaign}/schedule', [CampaignController::class, 'schedule']);
        Route::post('/campaigns/{campaign}/start', [CampaignController::class, 'start']);
        Route::post('/campaigns/{campaign}/complete', [CampaignController::class, 'complete']);

        Route::get('/workflows', [WorkflowController::class, 'index']);
        Route::post('/workflows', [WorkflowController::class, 'store']);
        Route::post('/workflows/trigger', [WorkflowController::class, 'trigger']);
        Route::get('/workflow-runs', [WorkflowController::class, 'runs']);

        Route::get('/ai/tools', [AiAssistantController::class, 'tools']);
        Route::get('/ai/conversations', [AiAssistantController::class, 'conversations']);
        Route::post('/ai/conversations', [AiAssistantController::class, 'createConversation']);
        Route::get('/ai/conversations/{conversation}', [AiAssistantController::class, 'show']);
        Route::post('/ai/conversations/{conversation}/chat', [AiAssistantController::class, 'chat']);
    });
});
