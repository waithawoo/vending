<?php

namespace App\Entity;

class User
{
    public ?int $id;
    public string $email;
    public string $password;
    public string $role;
    public ?\DateTime $createdAt;
    public ?\DateTime $updatedAt;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_USER = 'user';

    public function __construct(
        string $email,
        string $password,
        string $role = self::ROLE_USER,
        ?int $id = null,
        ?\DateTime $createdAt = null,
        ?\DateTime $updatedAt = null
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
        $this->createdAt = $createdAt ?? new \DateTime();
        $this->updatedAt = $updatedAt ?? new \DateTime();
    }
    
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }
}
