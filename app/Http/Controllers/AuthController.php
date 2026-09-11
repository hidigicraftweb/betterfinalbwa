<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    private $authService;

    // Constructor to inject the AuthService
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    // Register a new user
    public function register(RegisterRequest $request)
    {
        $user = $this->authService->register($request->validated());
        return response()->json(['message' => 'User registered successfully.', 'user' => $user], 201);
    }

    // Login a user
    public function login(LoginRequest $request)
    {
        return $this->authService->login($request->validated());
    }

    // Token-based login for API
    public function tokenLogin(LoginRequest $request)
    {
        return $this->authService->tokenLogin($request->validated());
    }

    // Logout a user
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return response()->json(['message' => 'Logged out successfully.']);
    }

    public function user(Request $request)
    {
        return response()->json(new UserResource($request->user()));
    }
}
