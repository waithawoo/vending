<?php

namespace App\Controller\Api\V1;

use App\Core\JwtManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\AuthService;

class AuthController extends ApiController
{
    private AuthService $authService;

    public function __construct(AuthService $authService) {
        $this->authService = $authService;
    }

    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $this->validateRegistrationData($data['email'] ?? '', $data['password'] ?? '');
        $user = $this->authService->authenticate($data['email'], $data['password']);
        if ($user) {
            $token = JwtManager::generateToken($user);
            return new JsonResponse(['token' => $token]);
        } else {
            return new JsonResponse(['message' => 'Invalid credentials'], 401);
        }
    }

    public function register(Request $request): JsonResponse 
    {
        $data = json_decode($request->getContent(), true);
        $this->validateRegistrationData($data['email'] ?? '', $data['password'] ?? '');
        $this->authService->register($data['email'], $data['password']);
        return new JsonResponse(['message' => 'User created'], 201);
    }

    private function validateRegistrationData(string $email, string $password): void
    {
        if (empty($email)) {
            throw new \InvalidArgumentException('Email is required');
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }

        if (empty($password)) {
            throw new \InvalidArgumentException('Password is required');
        } elseif (strlen($password) < 6) {
            throw new \InvalidArgumentException('Password must be at least 6 characters long');
        }
    }
}
