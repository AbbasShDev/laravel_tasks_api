<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller {

    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        if ( ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Your current password is not correct', 401]);
        }

        $validatedData = $request->validate([
            'password'                  => ['required'],
            'new_password'              => ['required', 'confirmed'],
            'new_password_confirmation' => ['required']
        ]);

        $user->password = bcrypt($validatedData['new_password']);

        if ($user->save()) {
            return ['message' => 'Password updated successfully'];
        }

        return response()->json(['message' => 'Something went wrong, try again'], 500);
    }

    public function updateProfile(Request $request)
    {
        $validatedData = $request->validate([
            'name'  => ['string', 'required'],
            'email' => ['email', 'required', Rule::unique('users')->ignore(auth()->id())],
        ]);

        if (auth()->user()->update($validatedData)) {
            return ['message' => 'Profile updated successfully'];
        }

        return response()->json(['message' => 'Something went wrong, try again'], 500);
    }
}
