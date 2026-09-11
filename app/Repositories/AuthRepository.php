<?php

namespace App\Repositories;

use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthRepository
{
    // Register a new user
    public function register($data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'photo' => $data['photo'],
            'gender' => $data['gender'],
            'password' => Hash::make($data['password']),
        ]);

        $user->assignRole('customer');

        return $user->load('roles');
    }

    // Login a user
    public function login(array $data)
    {
        $credentials = [
            'email' => $data['email'],
            'password' => $data['password'],
        ];

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'The provided credentials do not match our records.'], 401);
        }

        request()->session()->regenerate();

        $user = Auth::user();

        return response()->json([
            'message' => 'Login successful',
            'user' => new UserResource($user->load('roles')),
        ]);
    }

    // Token-based login for API
    public function tokenLogin(array $data)
    {
        if (!Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => new UserResource($user->load('roles')),
        ]);
    }
}