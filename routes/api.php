<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;       
use App\Http\Controllers\TrackerFormController; 


Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/login', [AuthController::class, 'login']);



// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Tracker Form Routes
    Route::prefix('tracker-forms')->group(function () {
        Route::get('/', [TrackerFormController::class, 'index']);
        Route::post('/', [TrackerFormController::class, 'store']);
        Route::get('/stats', [TrackerFormController::class, 'stats']);
        Route::get('/export', [TrackerFormController::class, 'export']);
        Route::post('/bulk-delete', [TrackerFormController::class, 'bulkDelete']);
        Route::post('/{id}/complete', [TrackerFormController::class, 'markAsCompleted']);
        
        Route::get('/{id}', [TrackerFormController::class, 'show']);
        Route::put('/{id}', [TrackerFormController::class, 'update']);
        Route::delete('/{id}', [TrackerFormController::class, 'destroy']);
    });
});

// Test route (optional)
Route::get('/test', function () {
    return response()->json([
        'message' => 'API is working!',
        'version' => '1.0.0'
    ]);
});

