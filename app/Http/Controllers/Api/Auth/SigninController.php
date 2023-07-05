<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SigninController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('username', $request->get('username'))->first();

        if (!$user || !Hash::check($request->get('password'), $user->password)) {
            return response()->json([
                'status' => 'invalid',
                'message' => 'Wrong username or password',
            ], 401);
        }

        // Update last_login_at property
        $user->update([
            'last_login_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'token' => $user->createToken('api')->plainTextToken,
            'role' => $user->role->name,
            'privileges' => $user->role->privileges->pluck('name')->toArray()
        ]);
    }
}
