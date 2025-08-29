<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;

class AuthService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function authenticate(string $email, string $password): ?User
    {
        $user = $this->userRepository->findByEmail($email);
        if ($user && $user->verifyPassword($password)) {
            return $user;
        }
        return null;
    }

    public function register(string $email, string $password, string $role = User::ROLE_USER): User
    {
        if ($this->userRepository->findByEmail($email)) {
            throw new \InvalidArgumentException('Email already exists');
        }
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $user = new User($email, $passwordHash, $role);
        return $this->userRepository->create($user);
    }
}
