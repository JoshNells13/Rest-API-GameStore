<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class administrator extends Authenticatable
{
     use HasApiTokens, HasFactory, Notifiable;

     protected $fillable = ['username', 'password', 'last_login_at'];

     protected $hidden = [
         'password',
         'remember_token',
     ];

     protected function casts(): array
     {
         return [
             'password' => 'hashed',
             'last_login_at' => 'datetime',
         ];
     }
}
