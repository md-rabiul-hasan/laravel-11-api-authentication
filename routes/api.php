<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Middleware\JwtAuthenticate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::middleware([JwtAuthenticate::class])->group(function(){

    Route::group(['prefix' => 'v1'], function(){
        Route::group(['prefix' => 'auth'], function(){        
            Route::post('login', [AuthController::class, 'login'])->withoutMiddleware([JwtAuthenticate::class]);    
            Route::get('me', [AuthController::class, 'me']);
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('refresh-token', [AuthController::class,'refresh']);
        });
    });


});