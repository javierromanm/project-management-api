<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\DeveloperController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Middleware\UserIsAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('/clients', ClientController::class)->middleware(['auth:sanctum', UserIsAdmin::class]);

Route::apiResource('/developers', DeveloperController::class)->middleware(['auth:sanctum', UserIsAdmin::class]);

Route::apiResource('/projects', ProjectController::class)->middleware(['auth:sanctum', UserIsAdmin::class]);

Route::apiResource('/tasks', TaskController::class)->middleware(['auth:sanctum', UserIsAdmin::class]);