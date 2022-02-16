<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthenticationController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = auth()->user();
            $token = $user->createToken('api')->plainTextToken;

            return response()->json([
                'message' => 'your login was successfully',
                'token' => $token
            ], 200);
        } else {
            return response()->json(array('message' => 'your login was not successfully'), 403);
        };
    }
}
