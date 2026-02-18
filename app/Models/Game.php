<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Game extends Model
{
    use HasApiTokens;


    protected $fillable = ['title', 'slug', 'description', 'created_by', 'thumbnail'];

    public function Author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function versions()
    {
        return $this->hasMany(Gameversion::class);
    }



}
