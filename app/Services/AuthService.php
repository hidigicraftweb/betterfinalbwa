<?php

namespace App\Services;

use App\Repositories\AuthRepository;
use App\Models\User;
use Illuminate\Http\Upload\UploadedFile;

class AuthService
{
    protected $authRepository;

    // Constructor to inject the AuthRepository
    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    // Register a new user
    public function register(array $data)
    {
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }

        return $this->authRepository->register($data);
    }

    // Login a user
    public function login(array $data)
    {
        return $this->authRepository->login($data);
    }
    
    // Token-based login for API
    public function tokenLogin(array $data)
    {
        return $this->authRepository->tokenLogin($data);
    }

    // Upload user photo
    protected function uploadPhoto(UploadedFile $photo)
    {
        return $photo->store('users', 'public');
    }
}