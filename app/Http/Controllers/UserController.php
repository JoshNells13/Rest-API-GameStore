<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\administrator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function GetAdmin(Request $request)
    {
        $user = administrator::all();

        $checkAdmin = administrator::where('id', $request->user()->id)->first();

        if (!$checkAdmin) {
            return response([
                'status' => 'forbidden',
                'message' => 'You are not the administrator'
            ], 403);
        }

        return response([
            'totalelement' => $user->count(),
            'content' => $user
        ], 201);
    }


    public function store(RegisterRequest $request)
    {

        $checkAdmin = administrator::where('id', $request->user()->id)->first();

        if (!$checkAdmin) {
            return response([
                'status' => 'forbidden',
                'message' => 'You are not the administrator'
            ], 403);
        }

        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);


        return response([
            'status' => 'success',
            'username' => $user->username
        ], 201);
    }


    public function update(RegisterRequest $request, $id)
    {
        $checkAdmin = administrator::where('id', $request->user()->id)->first();

        if (!$checkAdmin) {
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

        $user->update([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);


        return response([
            'status' => 'success',
            'username' => $user->username
        ], 201);
    }


    public function destroy(Request $request, $id)
    {

        $checkAdmin = administrator::where('id', $request->user()->id)->first();

        if (!$checkAdmin) {
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


    public function show($user){
        $user = User::where('username', $user)->with(['Game','Score'])->first();

        if(!$user){
            return response([
                'message' => 'User Not Found'
            ],404);
        }

        return response([
            'Data' => $user
        ],200);
    }
}
