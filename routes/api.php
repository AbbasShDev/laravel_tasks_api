<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->prefix('user')->group(function (){

    Route::post('update/password', [UserController::class, 'updatePassword']);
    Route::post('update/profile', [UserController::class, 'updateProfile']);

});
