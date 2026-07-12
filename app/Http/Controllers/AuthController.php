<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = Auth::guard('api')->login($user);

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $token = Auth::guard('api')->attempt($credentials);

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => Auth::guard('api')->user(),
                'token' => $token,
            ],
        ]);
    }

    public function googleRedirect()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function googleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::updateOrCreate(
            ['google_id' => $googleUser->id],
            [
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'avatar_url' => $googleUser->avatar,
            ]
        );

        $token = Auth::guard('api')->login($user);

        return response()->json([
            'success' => true,
            'message' => 'Google login successful',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ]);
    }

    public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    public function user()
    {
        return response()->json([
            'success' => true,
            'data' => Auth::guard('api')->user(),
        ]);
    }
}
