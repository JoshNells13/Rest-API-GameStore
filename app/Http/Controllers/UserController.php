<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\administrator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use function Symfony\Component\Clock\now;

class UserController extends Controller
{
    public function getadmin(Request $request)
    {
        $admin = User::where('role','admin')->get();

        if ($request->user()->role !== 'admin') {
            return response([
                'status' => 'forbidden',
                'message' => 'You are not the administrator'
            ], 403);
        }

        return response([
            'totalelement' => $admin->count(),
            'content' => $admin
        ], 201);
    }

    public function getUser()
    {
        $user = User::where('role', '!=','admin')->get();

        return response([
            'user' => $user
        ], 200);
    }


    public function store(RegisterRequest $request)
    {

        if ($request->user()->role !== 'admin') {
            return response([
                'status' => 'forbidden',
                'message' => 'You are not the administrator'
            ], 403);
        }

        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'last_login_at' => now()
        ]);


        return response([
            'status' => 'success',
            'username' => $user->username
        ], 201);
    }


    public function update(Request $request, $username)
    {
        $data = $request->validate([
            'username' => 'required|string|max:255',
            'password' => 'nullable|min:6'
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }


        if ($request->user()->role !== 'admin') {
            return response([
                'status' => 'forbidden',
                'message' => 'You are not the administrator'
            ], 403);
        }

        $user = User::where('username', $username)->first();

        if (!$user) {
            return response([
                'status' => 'not-found',
                'message' => 'user Not found'
            ], 403);
        }

        $user->update($data);


        return response([
            'status' => 'success',
            'username' => $user->username
        ], 201);
    }


    public function destroy(Request $request, $id)
    {

        if ($request->user()->role !== 'admin') {
            return response([
                'status' => 'forbidden',
                'message' => 'You are not the administrator'
            ], 403);
        }

        $user = User::find($id);

        if (!$user) {
            return response([
                'status' => 'not-found',
                'message' => 'user Not found'
            ], 403);
        }

        $user->delete();

        return response()->noContent();
    }


    public function show($user)
    {
        $user = User::where('username', $user)->with(['Game', 'Score'])->first();

        if (!$user) {
            return response([
                'message' => 'User Not Found'
            ], 404);
        }

        return response([
            'Data' => $user
        ], 200);
    }
}
