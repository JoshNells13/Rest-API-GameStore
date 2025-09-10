<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function SignUp(Request $request){
        $request->validate([
            'username' => 'required|min:4|max:60|unique:users,username',
            'password' => 'required|min:5|max:10',
            'role' => 'required'
        ]);

        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        return response([
            'status' => 'success',
            'token' => $user->createToken('sign_tokens')->plainTextToken,
        ],201);
    }

    public function Login(Request $request){
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('username', $request->username)->first();

        if(!$user || !Hash::check($request->password,$user->password)){
            return response([
                'status' => 'invalid',
                'message' => 'Wrong Password or Username'
            ],401);
        }


        return response([
            'status' => 'success',
            'token' => $user->createToken('login_tokens')->plainTextToken
        ],201);
    }   

    public function SignOut(Request $request){
        $request->user()->currentAccessToken()->delete();

        return response([
            'status' => 'success'
        ]);
    }
}
