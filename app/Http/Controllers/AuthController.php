<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\administrator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function SignUp(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'last_login_at' => now(),
            ]);

            $token = $user->createToken('sign_tokens')->plainTextToken;

            return response([
                'status' => 'success',
                'token' => $token,
                'role' => ($user instanceof administrator) ? 'administrator' : 'user',
                'username' => $user->username
            ], 200);
        } catch (\Exception $e) {
            return response([
                'status' => 'error',
                'message' => 'An error occurred during registration',
                'error' => $e->getMessage()
            ], 500);
        }
    }




    public function Signin(LoginRequest $request)
    {
        try {
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

            $user->update(['last_login_at' => now()]);

            $token = $user->createToken('login_token')->plainTextToken;

            return response([
                'status' => 'success',
                'token' => $token,
                'role' => ($user instanceof administrator) ? 'administrator' : 'user',
                'username' => $user->username
            ], 200);
        } catch (\Exception $e) {
            return response([
                'status' => 'error',
                'message' => 'An error occurred during login',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function SignOut(Request $request)
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
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
