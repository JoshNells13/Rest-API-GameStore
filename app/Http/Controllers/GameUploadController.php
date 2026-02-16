<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\gameversion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GameUploadController extends Controller
{
    public function GameFileUpload(Request $request, $slug)
    {

        $request->validate([
            'storage_path' => 'required|mimes:png,jpg'
        ]);

        $Game = Game::where('slug', $slug)->first();

        $CheckUser = Auth::user()->id;


        if (!$Game) {
            return response([
                'message' => 'Game Not Found'
            ], 404);
        }

        if(!Game::where('slug', $slug)->where('created_by', $CheckUser)->exists()){
            return response([
                'message' => 'Forbidden',
                'status' => 'Not Developer'
            ],403);
        }

        $file = $request->storage_path;
        $storage = $file->storeAs('games', $file->getClientOriginalName(), 'public');




        function generateVersion($version)
        {
            $version = Str::slug($version);
            $originalSlug = $version;
            $i = 1;


            while (gameversion::where('version', $version)->exists()) {
                $version = $originalSlug . '.' . $i;
                $i++;
            }

            return $version;
        }

        $major = 1;
        $minor = 1;
        $version_number = "{$major}.{$minor}";

        $version = generateVersion($version_number);
        $Data = gameversion::create([
            'game_id' => $Game->id,
            'storage_path' => $storage,
            'version' => $version
        ]);

        return response([
            'message' => 'Success',
            'data' => $Data
        ], 200);
    }


    public function GetGamesVersion(Request $request, $slug,$version){
        $GetGame = Game::where('slug', $slug)->first();

        if(!$GetGame){
            return response([
                'message' => 'Game Not found'
            ],404);
        }

        $GetVersion = gameversion::where('game_id', $GetGame->id)->where('version', $version)->first();


        if(!$GetVersion){
            return response([
                'message' => 'Game Not Found'
            ],404);
        }

        $filePath = storage_path('app/public/'. $GetVersion->storage_path);
        if(!file_exists($filePath)){
            return response([
                'message' => 'File Not Found'
            ],404);
        }

        return response()->file(
            $filePath
        );


    }
}
