<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register new user and issue token.
     */
    public function register(Request $request)
    {
        $validated = $request->validate(rules: [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create(attributes: [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make(value: $validated['password']),
            'role' => 'user',
        ]);

        $token = $user->createToken(name: 'api-token')->plainTextToken;

        return response()->json(data: [
            'user' => $user,
            'token' => $token,
        ], status: 201);
    }

    /**
     * Login user and issue token.
     */
    public function login(Request $request)
    {
        $validated = $request->validate(rules: [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where(column: 'email', operator: $validated['email'])->first();

        if (!$user || !Hash::check(value: $validated['password'], hashedValue: $user->password)) {
            throw ValidationException::withMessages(messages: [
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken(name: 'api-token')->plainTextToken;

        return response()->json(data: [
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Logout user (revoke current token).
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(data: ['message' => 'Logged out successfully']);
    }
}
