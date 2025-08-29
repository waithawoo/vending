<?php

namespace App\Repository;

use App\Core\Database;
use App\Entity\User;
use PDO;

class UserRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function all(): array 
    {
        $stmt = $this->db->query("SELECT * FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(User $user): User
    {
        $stmt = $this->db->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$user->email, $user->password, $user->role]);
        $user->id = (int)$this->db->lastInsertId();
        return $user;
    }

    public function delete(int $id): bool 
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function find(int $id): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? $this->mapToEntity($data) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        
        $data = $stmt->fetch();
        return $data ? $this->mapToEntity($data) : null;
    }

    private function mapToEntity(array $data): User
    {
        return new User(
            $data['email'],
            $data['password'],
            $data['role'],
            (int)$data['id'],
            new \DateTime($data['created_at'])
        );
    }
}
