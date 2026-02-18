<?php

namespace App\Http\Controllers;

use App\Http\Requests\GameRequest;
use App\Http\Requests\GetGameRequest;
use App\Http\Resources\GameResource;
use App\Models\Game;
use App\Models\gameversion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

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

    public function index(GetGameRequest $request)
    {
        try {
            $page = $request->input('page', 0);
            $size = $request->input('size', 10);
            $sortBy = $request->input('sortBy', 'title');
            $sortDir = $request->input('sortDir', 'asc');

            $query = Game::has('versions');

            switch ($sortBy) {
                case 'popular':
                    $query->withCount('scores')
                        ->orderBy('scores_count', $sortDir);
                    break;

                case 'uploaddate':
                    $query->withMax('versions', 'created_at')
                        ->orderBy('versions_max_created_at', $sortDir);
                    break;

                default:
                    $query->orderBy('title', $sortDir);
                    break;
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
        } catch (ValidationException $e) {
            return response([
                'message' => 'Invalid query parameters',
                'errors' => $e->errors()
            ], 422);
        }
    }


    public function store(GameRequest $request)
    {
        try {
            $slug = $this->generateUniqueSlug($request->title);

            $game = Game::create([
                'title' => $request->title,
                'description' => $request->description,
                'created_by' => $request->user()->id,
                'slug' => $slug,
            ]);

            return response([
                'status' => 'success',
                'data' => $game->slug
            ], 201);
        } catch (\Exception $e) {
            return response([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function update(GameRequest $request, $slug)
    {
        try {
            $game = Game::where('slug', $slug)->first();

            if (!$game) {
                return response([
                    'message' => 'Game Not Found'
                ], 404);
            }

            if ($game->created_by !== $request->user()->id) {
                return response([
                    'message' => 'Forbidden'
                ], 403);
            }

            $data = [
                'title' => $request->title,
                'description' => $request->description,
            ];


            $game->update($data);

            return response([
                'message' => 'Update Game Success',
                'data' => new GameResource($game)
            ], 201);
        } catch (\Exception $e) {
            return response([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function show(Request $request, $slug)
    {
        try {
            $GamesDetail = Game::where('slug', $slug)->first();

            if (!$GamesDetail) {
                return  response([
                    'message' => 'Game Not Found'
                ], 404);
            }

            return response([
                'game' => GameResource::make($GamesDetail)
            ], 200);
        } catch (\Exception $e) {
            return response([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function destroy(Request $request, $slug)
    {
        try {
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
        } catch (\Exception $e) {
            return response([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
