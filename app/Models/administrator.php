<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class administrator extends Model
{
     use HasApiTokens,HasFactory;

     protected $fillable = ['username','password'];
}
