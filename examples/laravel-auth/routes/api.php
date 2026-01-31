<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Public auth routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::get('/auth/verify-email', [AuthController::class, 'verifyEmail']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/verify-2fa', [AuthController::class, 'verify2fa']);
Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users/me', function () {
        $user = auth()->user();
        return response()->json([
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'email_verified' => (bool) $user->email_verified_at,
            'two_factor_enabled' => $user->two_factor_enabled,
        ]);
    });

    Route::post('/users/toggle-2fa', function () {
        $user = auth()->user();
        $user->update(['two_factor_enabled' => !$user->two_factor_enabled]);

        app(App\Services\VeilMailService::class)
            ->sendTwoFactorToggledEmail($user->email, $user->two_factor_enabled);

        return response()->json([
            'two_factor_enabled' => $user->two_factor_enabled,
            'message' => '2FA ' . ($user->two_factor_enabled ? 'enabled' : 'disabled'),
        ]);
    });
});
