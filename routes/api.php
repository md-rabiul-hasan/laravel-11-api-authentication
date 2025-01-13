<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::group(['prefix' => 'v1'], function(){
    Route::get('hello', function(){
        return Hash::make('12345678');
    });

    Route::group(['prefix' => 'auth'], function(){
        Route::post('login', [AuthController::class, 'login']);
    });
});