<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class SignupController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required|min:4|max:60|unique:users',
                'email' => 'required|email|unique:users|ends_with:@lpulaguna.edu.ph',
                'password' => 'required|min:8|max:65536',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'errors' => $e->validator->getMessageBag(),
            ], 422);
        }

        $user = new User();
        $user->username = $request->get('username');
        $user->email = $request->get('email');
        $user->password = Hash::make($request->get('password'));
        $user->role_id = Role::where('name', 'Viewer')->first()->id;
        $user->save();

        return response()->json([
            'status' => 'success',
            'token' => $user->createToken('api')->plainTextToken,
        ], 201);
    }
}
