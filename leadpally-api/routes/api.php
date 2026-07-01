<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Teams\TeamController;
use App\Http\Controllers\Api\V1\Search\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => response()->json(['status'=>'healthy','timestamp'=>now()->toIso8601String()]));

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
    });
});
