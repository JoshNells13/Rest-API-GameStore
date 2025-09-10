<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Game extends Model
{
    use HasApiTokens;


    protected $fillable = ['title','slug','description','created_by','thumbnail'];

    public function User(){
        return $this->belongsTo(User::class,'created_by');
    }

   public function gameversions() {
    return $this->hasMany(Gameversion::class);
}

}
