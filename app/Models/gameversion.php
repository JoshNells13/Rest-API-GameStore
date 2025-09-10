<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class gameversion extends Model
{
    use HasApiTokens;

    protected $fillable = ['game_id','version','storage_path'];

    public function Game(){
        return $this->belongsTo(Game::class,'game_id');
    }
    
    public function Score(){
        return $this->hasMany(Score::class,'game_version_id');
    }

}
