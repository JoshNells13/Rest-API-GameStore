<?php

namespace App\Http\Controllers;

use App\Models\administrator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthAdminController extends Controller
{
    public function Login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = administrator::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'status' => 'invalid',
                'message' => 'Wrong Password or Username'
            ], 401);
        }


        return response([
            'status' => 'success',
            'token' => $user->createToken('login_tokens')->plainTextToken
        ], 201);
    }


     public function Signup(Request $request)
    {
        $request->validate([
            'username' => 'required|min:4|max:60|unique:administrators,username',
            'password' => 'required|min:5|max:10',
        ]);

        $checkAdmin = administrator::where('id', $request->user()->id)->first();

        if (!$checkAdmin) {
            return response([
                'status' => 'forbidden',
                'message' => 'You are not the administrator'
            ], 403);
        }

        $user = administrator::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);


        return response([
            'status' => 'success',
            'username' => $user->username
        ], 201);
    }
}
