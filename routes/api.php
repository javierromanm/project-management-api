<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\DeveloperController;
use App\Http\Middleware\UserIsAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::resource('/clients', ClientController::class)->middleware(['auth:sanctum', UserIsAdmin::class]);

Route::resource('/developers', DeveloperController::class)->middleware(['auth:sanctum', UserIsAdmin::class]);