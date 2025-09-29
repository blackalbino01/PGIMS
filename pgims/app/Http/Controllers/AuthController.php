<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register new user and issue API token.
     *
     * Registers a new user with the given name, email, and password.
     * Returns the created user data and auth token.
     *
     * @bodyParam name string required User's name. Example: John Doe
     * @bodyParam email string required Unique user email address. Example: john@example.com
     * @bodyParam password string required Password (min 8 chars). Example: password123
     * @bodyParam password_confirmation string required Password confirmation. Example: password123
     *
     * @response 201 {
     *   "user": {
     *       "id": 1,
     *       "name": "John Doe",
     *       "email": "john@example.com",
     *       "role": "user",
     *       "created_at": "2025-09-17T14:00:00Z",
     *       "updated_at": "2025-09-17T14:00:00Z"
     *   },
     *   "token": "encrypted_api_token_here"
     * }
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
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
            'role' => 'staff',
        ]);

        $token = $user->createToken(name: 'api-token')->plainTextToken;

        return response()->json(data: [
            'user' => $user,
            'token' => $token,
        ], status: 201);
    }

    /**
     * Login existing user and issue API token.
     *
     * Authenticates user by email and password.
     * Returns user data and an authentication token.
     *
     * @bodyParam email string required User email address. Example: john@example.com
     * @bodyParam password string required User password. Example: password123
     *
     * @response {
     *   "user": {
     *       "id": 1,
     *       "name": "John Doe",
     *       "email": "john@example.com",
     *       "role": "user",
     *       "created_at": "2025-09-17T14:00:00Z",
     *       "updated_at": "2025-09-17T14:00:00Z"
     *   },
     *   "token": "encrypted_api_token_here"
     * }
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws ValidationException when credentials are invalid
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
     * Logout authenticated user by revoking current access token.
     *
     * Requires authentication.
     *
     * @response {
     *   "message": "Logged out successfully"
     * }
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(data: ['message' => 'Logged out successfully']);
    }
}
