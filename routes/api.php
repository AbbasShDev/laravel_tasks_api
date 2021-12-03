<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function (){

    Route::prefix('user')->group(function (){
        Route::post('update/password', [UserController::class, 'updatePassword']);
        Route::post('update/profile', [UserController::class, 'updateProfile']);
    });

    Route::apiResource('categories', CategoryController::class);
    Route::patch('categories/{categoryId}/restore', [CategoryController::class, 'restore']);
    Route::delete('categories/{categoryId}/force-delete', [CategoryController::class, 'forceDelete']);

    Route::apiResource('tasks', TaskController::class);
    Route::patch('tasks/{taskId}/restore', [TaskController::class, 'restore']);
    Route::delete('tasks/{taskId}/force-delete', [TaskController::class, 'forceDelete']);

    Route::apiResource('comments', CommentController::class)->except(['index', 'show']);


});
