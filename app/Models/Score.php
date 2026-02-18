<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Score extends Model
{
    use HasApiTokens, HasFactory;

    protected $fillable = ['user_id', 'game_version_id', 'score'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gameversion()
    {
        return $this->belongsTo(Gameversion::class);
    }
}
