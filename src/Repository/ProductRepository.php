<?php

namespace App\Repository;

use App\Core\Database;
use App\Entity\Product;
use PDO;

class ProductRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function all(): array {
        $stmt = $this->db->query("SELECT * FROM products");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(Product $product): Product
    {
        $stmt = $this->db->prepare("INSERT INTO products (name, price, quantity_available) VALUES (?, ?, ?)");
        $stmt->execute([$product->name, $product->price, $product->quantityAvailable]);
        $product->id = (int)$this->db->lastInsertId();
        return $product;
    }

    public function update(Product $product): bool
    {
        $stmt = $this->db->prepare("UPDATE products SET name = ?, price = ?, quantity_available = ?, updated_at = ? WHERE id = ?");
        return $stmt->execute([
            $product->name,
            $product->price,
            $product->quantityAvailable,
            (new \DateTime())->format('Y-m-d H:i:s'),
            $product->id,
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function find(int $id): ?Product
    {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? $this->mapToEntity($data) : null;
    }

    public function paginate(int $limit = 20, int $offset = 0, string $sortBy = 'name', string $sortDirection = 'ASC'): array
    {
        $allowedSortColumns = ['name', 'price', 'quantity_available', 'created_at'];
        $sortBy = in_array($sortBy, $allowedSortColumns) ? $sortBy : 'name';
        $sortDirection = strtoupper($sortDirection) === 'DESC' ? 'DESC' : 'ASC';
        
        $sql = "SELECT * FROM products ORDER BY {$sortBy} {$sortDirection} LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }

    public function count(): int
    {
        $sql = "SELECT COUNT(*) FROM products";
        $stmt = $this->db->query($sql);
        return (int)$stmt->fetchColumn();
    }

    private function mapToEntity(array $data): Product
    {
        return new Product(
            $data['name'],
            $data['price'],
            $data['quantity_available'],
            (int)$data['id'],
            new \DateTime($data['created_at'])
        );
    }
}
