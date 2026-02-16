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
         $game = Game::where('slug', trim($slug))->first();

        if (!$game) {
            return response([
                'message' => 'Game not found'
            ], 404);
        }

            $gameVersionIds = $game->gameversions()->pluck('id');

        // Ambil skor tertinggi tiap user untuk game ini
        $scores = Score::select('user_id', FacadesDB::raw('MAX(score) as max_score'))
            ->whereIn('game_version_id', $gameVersionIds)
            ->groupBy('user_id')
            ->with('user')
            ->orderByDesc('max_score')
            ->get()
            ->map(function ($row) {
                return [
                    'username' => $row->user->username,
                    'score' => $row->max_score,
                    'timestamp' => $row->user->created_at
                ];
            });

        return response([
            'scores' => $scores
        ], 200);
    }

    public function StoreGameScore(Request $request, $slug)
    {
        $request->validate([
            'score' => 'required|integer|min:0'
        ]);

        $game = Game::where('slug', trim($slug))->first();

        if (!$game) {
            return response([
                'message' => 'Game not found'
            ], 404);
        }

        $latestVersion = $game->gameversions()->latest('created_at')->first();

        if (!$latestVersion) {
            return response([
                'message' => 'No game version available'
            ], 400);
        }

        $score = Score::create([
            'user_id' => $request->user()->id,
            'game_version_id' => $latestVersion->id,
            'score' => $request->score
        ]);

        return response([
            'score' => $score
        ], 201);
    }


}
