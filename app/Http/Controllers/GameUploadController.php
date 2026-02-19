<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\gameversion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ZipArchive;
use Laravel\Sanctum\PersonalAccessToken;

class GameUploadController extends Controller
{
    public function GameFileUpload(Request $request, $slug)
    {
        try {
            $request->validate([
                'zipfile' => 'required|file|mimes:zip',
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);


            // 1️⃣ Validate required fields (zipfile + token)
            if (!$request->hasFile('zipfile') || !$request->token) {
                return response("Invalid request", 400);
            }

            // 2️⃣ Validate token from form param (NOT header)
            $accessToken = PersonalAccessToken::findToken($request->token);

            if (!$accessToken) {
                return response("Invalid token", 401);
            }

            $user = $accessToken->tokenable;


            $game = Game::where('slug', $slug)->first();

            if (!$game) {
                return response("Game not found", 404);
            }


            if ($user->id !== $game->created_by) {
                return response("User is not author of the game", 403);
            }


            $latestVersion = gameversion::where('game_id', $game->id)->max('version') ?? 0;
            $newVersion = $latestVersion + 1;


            $destinationPath = public_path("games/{$slug}/{$newVersion}");

            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0777, true);
            }


            $zip = new ZipArchive;
            $zipFile = $request->file('zipfile')->getRealPath();

            if ($zip->open($zipFile) === TRUE) {
                $zip->extractTo($destinationPath);
                $zip->close();
            } else {
                return response("Failed to extract zip file", 400);
            }


            gameversion::create([
                'game_id' => $game->id,
                'version' => $newVersion,
                'storage_path' => "/games/{$slug}/{$newVersion}/"
            ]);


            $game->update([
                'storage_path' => "/games/{$slug}/{$newVersion}/"
            ]);

            if($request->hasFile('thumbnail')){
                $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
                $game->update([
                    'thumbnail' => $thumbnailPath
                ]);
            }


            return response()->json([
                'status' => 'success'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    // public function GetGamesVersion(Request $request, $slug, $version)
    // {
    //     $GetGame = Game::where('slug', $slug)->first();

    //     if (!$GetGame) {
    //         return response([
    //             'message' => 'Game Not found'
    //         ], 404);
    //     }

    //     $GetVersion = gameversion::where('game_id', $GetGame->id)->where('version', $version)->first();


    //     if (!$GetVersion) {
    //         return response([
    //             'message' => 'Game Not Found'
    //         ], 404);
    //     }

    //     $filePath = storage_path('app/public/' . $GetVersion->storage_path);
    //     if (!file_exists($filePath)) {
    //         return response([
    //             'message' => 'File Not Found'
    //         ], 404);
    //     }

    //     return response()->file(
    //         $filePath
    //     );
    // }
}
