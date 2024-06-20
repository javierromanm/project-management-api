<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\DeveloperController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Middleware\UserIsAdmin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', function(Request $request){
    $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)){
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    $token = $user->createToken('app-token')->plainTextToken;

    return response()->json(['token' => $token]);
    
});

Route::Resource('/clients', ClientController::class)->middleware(['auth:sanctum', UserIsAdmin::class]);

Route::apiResource('/developers', DeveloperController::class)->middleware(['auth:sanctum', UserIsAdmin::class]);

Route::apiResource('/projects', ProjectController::class)->middleware(['auth:sanctum', UserIsAdmin::class]);

Route::apiResource('/tasks', TaskController::class)->middleware(['auth:sanctum', UserIsAdmin::class]);