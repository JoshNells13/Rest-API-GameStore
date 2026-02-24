<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function signUp(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => 'player', // default role
                'last_login_at' => now(),
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response([
                'status' => 'success',
                'token' => $token,
                'user' => [
                    'username' => $user->username,
                    'role' => $user->role
                ]
            ], 201);

        } catch (\Exception $e) {
            return response([
                'status' => 'error',
                'message' => 'Registration failed',
            ], 500);
        }
    }

    public function signIn(LoginRequest $request)
    {
        try {
            $user = User::where('username', $request->username)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response([
                    'status' => 'invalid',
                    'message' => 'Wrong username or password'
                ], 401);
            }

            $user->update([
                'last_login_at' => now()
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response([
                'status' => 'success',
                'token' => $token,
                'user' => [
                    'username' => $user->username,
                    'role' => $user->role
                ]
            ], 200);

        } catch (\Exception $e) {
            return response([
                'status' => 'error',
                'message' => 'Login failed',
            ], 500);
        }
    }

    public function signOut(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response([
                'status' => 'success',
                'message' => 'Successfully signed out'
            ], 200);

        } catch (\Exception $e) {
            return response([
                'status' => 'error',
                'message' => 'Failed to sign out',
            ], 500);
        }
    }
}
