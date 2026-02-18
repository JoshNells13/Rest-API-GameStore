<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\administrator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function SignUp(UserRequest $request)
    {

        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        return response([
            'status' => 'success',
            'token' => $user->createToken('sign_tokens')->plainTextToken,
        ], 201);
    }

    public function login(UserRequest $request)
    {

        // Cek ke tabel users
        $user = User::where('username', $request->username)->first();

        // Kalau tidak ada di users, cek ke admins
        if (!$user) {
            $user = administrator::where('username', $request->username)->first();
        }


        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'status' => 'invalid',
                'message' => 'Wrong Username or Password'
            ], 401);
        }

        return response([
            'status' => 'success',
            'token' => $user->createToken('login_token')->plainTextToken
        ], 200);
    }

    public function SignOut(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response([
            'status' => 'success'
        ], 200);
    }
}
