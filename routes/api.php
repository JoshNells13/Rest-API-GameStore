<?php

use App\Http\Controllers\AuthAdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GameUploadController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\UserController;
use App\Models\Score;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {


    Route::post('auth/signup', [AuthController::class, 'signup']);
    Route::post('auth/signin', [AuthController::class, 'signin']);

    Route::middleware('auth:sanctum')->group(function () {

        Route::post('auth/signout', [AuthController::class, 'signout']);


        Route::get('admins', [UserController::class, 'getAdmins']);


        Route::get('users', [UserController::class, 'getUsers']);
        Route::post('users', [UserController::class, 'store']);
        Route::put('users/{id}', [UserController::class, 'update']);
        Route::delete('users/{id}', [UserController::class, 'destroy']);
        Route::get('users/{username}', [UserController::class, 'show']);


        Route::get('games', [GameController::class, 'index']);
        Route::post('games', [GameController::class, 'store']);
        Route::get('games/{slug}', [GameController::class, 'show']);
        Route::put('games/{slug}', [GameController::class, 'update']);
        Route::delete('games/{slug}', [GameController::class, 'destroy']);


        Route::post('games/{slug}/upload', [GameUploadController::class, 'upload']);

        Route::get('games/{slug}/scores', [ScoreController::class, 'index']);
        Route::post('games/{slug}/scores', [ScoreController::class, 'store']);
    });
});
