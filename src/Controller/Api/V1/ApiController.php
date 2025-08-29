<?php

namespace App\Controller\Api\V1;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Core\JwtManager;

abstract class ApiController
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    protected function requireAuth(Request $request): User
    {
        $user = $this->isAuthenticated($request);
        if (!$user) {
            throw new \RuntimeException('Authentication required', 401);
        }
        return $user;
    }

    protected function requireAdmin(Request $request): User
    {
        $user = $this->requireAuth($request);
        if (!$user->isAdmin()) {
            throw new \RuntimeException('Admin access required', 403);
        }
        return $user;
    }

    protected function isAuthenticated(Request $request): User|null
    {
        $authHeader = $request->headers->get('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }

        $token = substr($authHeader, 7);
        $payload = JwtManager::decodeToken($token);
        if (!$payload) {
            return null;
        }

        $user = $this->userRepository->find($payload->sub);
        if (!$user) {
            return null;
        }
        return $user;
    }
}
