<?php

use App\Http\Controllers\AuthAdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GameUploadController;
use App\Http\Controllers\UserController;
use App\Models\Score;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function(){


    Route::post('auth/login',[AuthController::class,'Login']);
    Route::post('auth/signup',[AuthController::class,'SignUp']);




    Route::middleware('auth:sanctum')->group(function(){



        Route::post('auth/signout',[AuthController::class,'SignOut']);

        Route::get('users',[UserController::class,'GetAdmin']);
        Route::post('users',[UserController::class,'AddUser']);


        Route::put('users/{id}',[UserController::class,'UpdateUser']);
        Route::delete('users/{id}',[UserController::class,'DeleteUser']);


        Route::get('games',[GameController::class,'GetGame']);
        Route::post('games',[GameController::class,'AddGame']);
        Route::get('games/{slug}',[GameController::class,'GetDetailGames']);

        Route::post('games/{slug}/upload',[GameUploadController::class,'GameFileUpload']);
        Route::get('games/{slug}/{version}',[GameUploadController::class,'GetGamesVersion']);


        Route::put('games/{slug}',[GameController::class,'UpdateGame']);

        Route::delete('games/{slug}',[GameController::class,'DeleteGames']);
        Route::get('user/{username}',[UserController::class,'GetDetailUser']);

        Route::get('games/{slug}/scores',[Score::class,'GetGameScore']);
        Route::post('games/{slug}/scores');

    });
});
