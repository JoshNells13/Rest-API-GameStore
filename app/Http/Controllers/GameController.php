<?php

namespace App\Http\Controllers;

use App\Http\Requests\GameRequest;
use App\Http\Resources\GameResource;
use App\Models\Game;
use App\Models\gameversion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function index(Request $request)
    {
        $request->validate([
            'page' => 'nullable|integer|min:0',
            'size' => 'nullable|integer|min:1',
            'sortBy' => 'nullable|in:title,popular,uploaddate',
            'sortDir' => 'nullable|in:asc,desc'
        ]);

        $page = $request->input('page', 0);
        $size = $request->input('size', 10);
        $sortBy = $request->input('sortBy', 'title');
        $sortDir = $request->input('sortDir', 'asc');

        $query = Game::has('versions'); // exclude game tanpa version

        if ($sortBy === 'title') {
            $query->orderBy('title', $sortDir);
        }

        if ($sortBy === 'popular') {
            $query->withCount('scores')
                ->orderBy('scores_count', $sortDir);
        }

        if ($sortBy === 'uploaddate') {
            $query->withMax('versions', 'created_at')
                ->orderBy('versions_max_created_at', $sortDir);
        }

        $totalElements = $query->count();

        $games = $query->skip($page * $size)
            ->take($size)
            ->get();

        return response([
            'page' => $page,
            'size' => $games->count(),
            'totalElements' => $totalElements,
            'content' => GameResource::collection($games)
        ], 200);
    }
    public function AddGame(GameRequest $request)
    {

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

    public function UpdateGame(GameRequest $request, $slug)
    {
        $Game = Game::where('slug', $slug)->first();

        if (!$Game) {
            return response([
                'message' => 'Game Not Found'
            ], 404);
        }

        $CheckUser = Auth::user()->id;

        if (!Game::where('slug', $slug)->where('created_by', $CheckUser)->exists()) {
            return response([
                'message' => 'Forbidden',
                'status' => 'Not Developer'
            ], 403);
        }

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

    public function GetDetailGames(Request $request, $slug)
    {
        $GamesDetail = Game::where('slug', $slug)->first();

        if (!$GamesDetail) {
            return  response([
                'message' => 'Game Not Found'
            ], 404);
        }

        return response([
            'game' => $GamesDetail
        ], 200);
    }


    public function DeleteGames(Request $request, $slug)
    {

        $Game = Game::where('slug', $slug)->first();

        $CheckUser = Auth::user()->id;


        if (!$Game) {
            return response([
                'message' => 'Game Not Found'
            ], 404);
        }


        if (!Game::where('slug', $slug)->where('created_by', $CheckUser)->exists()) {
            return response([
                'message' => 'Forbidden',
                'status' => 'Not Developer'
            ], 403);
        }


        $Game->delete();

        return response([], 204);
    }
}
