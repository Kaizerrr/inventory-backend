<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SignoutController extends Controller
{
    public function store(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return [
            'status' => 'success',
        ];
    }
}