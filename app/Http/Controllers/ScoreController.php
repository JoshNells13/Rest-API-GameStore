<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Score;
use Illuminate\Container\Attributes\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB as FacadesDB;

class ScoreController extends Controller
{
    public function GetGameScores($slug)
    {
        try {
            $game = Game::where('slug', trim($slug))->firstOrFail();

            $scores = Score::select(
                'users.username',
                FacadesDB::raw('MAX(scores.score) as max_score'),
                FacadesDB::raw('MAX(scores.created_at) as timestamp')
            )
                ->join('users', 'scores.user_id', '=', 'users.id')
                ->whereIn('game_version_id', $game->gameversions()->pluck('id'))
                ->groupBy('users.username')
                ->orderByDesc('max_score')
                ->get();

            return response([
                'scores' => $scores
            ]);
        } catch (\Exception $e) {
            return response([
                'error' => 'An error occurred while fetching scores.',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function StoreGameScore(Request $request, $slug)
    {

        try {
            $request->validate([
                'score' => 'required|integer|min:0'
            ]);

            $game = Game::where('slug', trim($slug))->firstOrFail();

            $latestVersion = $game->gameversions()
                ->latest('created_at')
                ->firstOrFail();

            $score = Score::create([
                'user_id' => $request->user()->id,
                'game_version_id' => $latestVersion->id,
                'score' => $request->score
            ]);

            return response([
                'data' => $score
            ], 201);
        } catch (\Exception $e) {
            return response([
                'error' => 'An error occurred while storing the score.',
                'message' => $e->getMessage()
            ], 500);
        };
    }
}
