<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index()
    {
        return User::all();
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        return $user;
    }

    /**
     * Store a newly created user.
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
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(data: null, status: 204);
    }
}
