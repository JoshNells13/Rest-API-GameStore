<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\gameversion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;
use Laravel\Sanctum\PersonalAccessToken;

class GameUploadController extends Controller
{
    public function GameFileUpload(Request $request, $slug)
    {
        try {

            if (!$request->hasFile('zipfile') || !$request->token) {
                return response("Missing required fields", 400);
            }

            $accessToken = PersonalAccessToken::findToken($request->token);
            if (!$accessToken) return response("Invalid token", 401);

            $user = $accessToken->tokenable;
            $game = Game::where('slug', $slug)->first();
            if (!$game) return response("Game not found", 404);

            if ($user->id !== $game->created_by)
                return response("User is not author", 403);

            $newVersion = (GameVersion::where('game_id', $game->id)->max('version') ?? 0) + 1;

            $zipFile = $request->file('zipfile');

            $zipPath = $zipFile->storeAs(
                "games/{$slug}/{$newVersion}",
                "{$slug}_v{$newVersion}.zip",
                'public'
            );
            gameversion::create([
                'game_id' => $game->id,
                'version' => $newVersion,
                'storage_path' => $zipPath
            ]);

            $game->update([
                'storage_path' => $zipPath
            ]);

            if ($request->hasFile('thumbnail')) {
                $game->update([
                    'thumbnail' => $request->file('thumbnail')->store('thumbnails', 'public')
                ]);
            }

            return response()->json(['status' => 'success'], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function GetGamesVersion($slug, $version)
    {
        $game = Game::where('slug', $slug)->firstOrFail();

        $gameVersion = GameVersion::where('game_id', $game->id)
            ->where('version', $version)
            ->firstOrFail();

        if (!Storage::disk('public')->exists($gameVersion->storage_path)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        return Storage::disk('public')->download(
            $gameVersion->storage_path,
            $game->slug . "_v{$version}.zip"
        );
    }
}
