<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\MeController;
use App\Http\Controllers\Api\Auth\RefreshTokenController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Middleware\JwtAuthenticate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



// Group routes under the JwtAuthenticate middleware for secured access
Route::middleware([JwtAuthenticate::class])->group(function () {

    // API versioning for better maintenance and future updates
    Route::group(['prefix' => 'v1'], function () {

        // Authentication-related routes under the 'auth' prefix
        Route::group(['prefix' => 'auth'], function () {
            // Login route Note: This route bypasses JwtAuthenticate middleware as users need to authenticate first
            Route::post('login', [LoginController::class, 'login'])->withoutMiddleware([JwtAuthenticate::class]);
            // Get the authenticated user's details
            Route::get('me', [MeController::class, 'me']);
            // Logout route to invalidate the user's session
            Route::post('logout', [LogoutController::class, 'logout']);
            // Refresh the JWT token
            Route::post('refresh-token', [RefreshTokenController::class, 'refresh']);
        });
    });
});
