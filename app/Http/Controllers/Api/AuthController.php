<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required','confirmed'],
            'password_confirmation' => ['required'],
        ]);

        $validatedData["password"] = bcrypt($validatedData["password"]);

        $user = User::create($validatedData);

        $accessToken = $user->createToken("authToken")->accessToken;

        return ["user" => $user, "access_token" => $accessToken];
    }

    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        if(! auth()->attempt($validatedData)){
            return  response()->json(['message'=> "Invalid credentials"], 401);
        }

        $accessToken = auth()->user()->createToken("authToken")->accessToken;

        return ["user" => auth()->user(), "access_token" => $accessToken];

    }
}
