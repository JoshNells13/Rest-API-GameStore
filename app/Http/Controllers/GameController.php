<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\gameversion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GameController extends Controller
{

    private function generateUniqueSlug($title)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $i = 1;

        while (Game::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $i;
            $i++;
        }

        return $slug;
    }

    public function GetGame(Request $request)
    {
        $request->validate([
            'page' => 'numeric|min:0',
            'size' => 'numeric|min:0|max:10'
        ]);

        $size = $request->input('size', 10);

        $Game = Game::orderBy('created_at', 'DESC')
            ->paginate($size)
            ->appends(request()->query());

        return response([
            'total_element' => $Game->count(),
            'pages' => $Game->currentPage(),
            'size' => $Game->perPage(),
            'data' => $Game
        ]);
    }


    public function AddGame(Request $request)
    {
        $request->validate([
            'title' => 'required|min:3|max:60|unique:games,title',
            'description' => 'required|max:200|',
            'thumbnail' => 'file|mimes:png,jpg'
        ]);


        if ($request->user()->role !== 'developer') {
            return response([
                'message' => 'forbidden',
                'status' => 'not developer'
            ], 403);
        }


        $file = $request->thumbnail;
        $thumbnail = $file->storeAs('games_thumbnail', $file->getClientOriginalName(), 'public');

        $slug = $this->generateUniqueSlug($request->title);

        $Game = Game::create([
            'title' => $request->title,
            'description' => $request->description,
            'created_by' => $request->user()->id,
            'slug' => $slug,
            'thumbnail' => $thumbnail,
        ]);

        return response([
            'message' => 'Add Game Sukses',
            'Game' => $Game
        ]);
    }

    public function UpdateGame(Request $request, $slug)
    {
        $Game = Game::where('slug', $slug)->first();

        if (!$Game) {
            return response([
                'message' => 'Game Not Found'
            ], 404);
        }

        if ($request->user()->role !== 'developer') {
            return response([
                'message' => 'forbidden',
                'status' => 'not developer'
            ], 403);
        }

        $request->validate([
            'title' => 'required|min:3|max:60|unique:games,title,' . $Game->id,
            'description' => 'required|max:200|',
            'thumbnail' => 'file|mimes:png,jpg'
        ]);

        // 🔧 Simpan thumbnail baru
        $file = $request->thumbnail;
        $thumbnail = $file->storeAs('games_thumbnail', $file->getClientOriginalName(), 'public');


        // 🔧 Update game
        $Game->update([
            'title' => $request->title,
            'description' => $request->description,
            'created_by' => $request->user()->id,
            'thumbnail' => $thumbnail,
        ]);

        return response([
            'message' => 'Update Game Sukses',
            'Game' => $Game
        ]);
    }

    public function GetDetailGames(Request $request,$slug){
        $GamesDetail = Game::where('slug', $slug)->first();

        if(!$GamesDetail){
            return  response([
                'message' => 'Game Not Found' 
            ],404);
        }

        return response([
            'game' => $GamesDetail 
        ],200);
    }


    public function DeleteGames(Request $request, $slug){
        
        $Game = Game::where('slug', $slug)->first();

        if($request->user()->role !== 'developer'){
            return response([
                'message' => 'Forbidden',
                'status' => 'Not Developer'
            ],403);
        }

        if(!$Game){
            return response([
                'message' => 'Game Not Found'
            ],404);
        }

        $Game->delete();

        return response([],204);

    }
}
