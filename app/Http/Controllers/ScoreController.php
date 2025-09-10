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


    public function AddScore() {}
}
