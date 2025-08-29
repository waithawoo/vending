<?php

namespace App\Core;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Entity\User;

class JwtManager {
    public static function generateToken(User $user) {
        $payload = [
            'sub' => $user->id,
            'role' => $user->role,
            'iat' => time(),
            'exp' => time() + 3600
        ];
        return JWT::encode($payload, $_ENV['JWT_SECRET'], $_ENV['JWT_ALGO'] ?? 'HS256');
    }

    public static function decodeToken($token) {
        return JWT::decode($token, new Key($_ENV['JWT_SECRET'], $_ENV['JWT_ALGO'] ?? 'HS256'));
    }
}
