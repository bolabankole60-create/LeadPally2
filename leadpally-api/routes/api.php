<?php

use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => response()->json([
    'status' => 'healthy',
    'timestamp' => now()->toIso8601String(),
]));
