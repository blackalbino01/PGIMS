<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     *
     * @response [
     *   {
     *     "id": 1,
     *     "name": "Jane Doe",
     *     "email": "jane@example.com",
     *     "role": "admin",
     *     "created_at": "2025-09-19T19:29:00Z",
     *     "updated_at": "2025-09-19T19:29:00Z"
     *   }
     * ]
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return User::all();
    }

    /**
     * Display the specified user.
     *
     * @urlParam user int required The ID of the user.
     *
     * @response {
     *   "id": 1,
     *   "name": "Jane Doe",
     *   "email": "jane@example.com",
     *   "role": "admin",
     *   "created_at": "2025-09-19T19:29:00Z",
     *   "updated_at": "2025-09-19T19:29:00Z"
     * }
     *
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(User $user)
    {
        return $user;
    }

    /**
     * Store a newly created user.
     *
     * @bodyParam name string required User's full name. Example: Jane Doe
     * @bodyParam email string required Unique email address. Example: jane@example.com
     * @bodyParam password string required Password, minimum 8 characters, must be confirmed. Example: password123
     * @bodyParam password_confirmation string required Password confirmation. Example: password123
     * @bodyParam role string Nullable User role. Example: admin
     *
     * @response 201 {
     *   "id": 1,
     *   "name": "Jane Doe",
     *   "email": "jane@example.com",
     *   "role": "admin",
     *   "created_at": "2025-09-19T19:29:00Z",
     *   "updated_at": "2025-09-19T19:29:00Z"
     * }
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate(rules: [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'sometimes|string',
        ]);

        $validated['password'] = Hash::make(value: $validated['password']);

        $user = User::create(attributes: $validated);

        return response()->json(data: $user, status: 201);
    }

    /**
     * Update the specified user.
     *
     * @urlParam user int required The ID of the user.
     * @bodyParam name string Nullable Updated user name.
     * @bodyParam email string Nullable Updated unique email address.
     * @bodyParam password string Nullable Updated password, minimum 8 characters, must be confirmed.
     * @bodyParam password_confirmation string Nullable Password confirmation.
     * @bodyParam role string Nullable Updated user role.
     *
     * @response {
     *   "id": 1,
     *   "name": "Jane Doe Updated",
     *   "email": "jane.updated@example.com",
     *   "role": "user",
     *   "created_at": "2025-09-19T19:29:00Z",
     *   "updated_at": "2025-09-19T19:45:00Z"
     * }
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate(rules: [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'sometimes|string',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make(value: $validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update(attributes: $validated);

        return response()->json(data: $user);
    }

    /**
     * Remove the specified user.
     *
     * @urlParam user int required The ID of the user.
     *
     * @response 204 {}
     *
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(data: null, status: 204);
    }
}