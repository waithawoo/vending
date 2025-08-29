<?php
namespace App\Repository;

use App\Core\Database;
use App\Entity\Transaction;
use PDO;

class TransactionRepository 
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function all(): array 
    {
        $stmt = $this->db->query("SELECT * FROM transactions");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function paginate(int $limit = 20, int $offset = 0): array
    {   
        $sql = "SELECT 
                    t.quantity,
                    t.total_price,
                    t.created_at AS transaction_date,
                    u.email AS user_email,
                    p.name AS product_name,
                    p.price AS product_price
                FROM transactions t
                JOIN users u ON t.user_id = u.id
                JOIN products p ON t.product_id = p.id
                ORDER BY t.created_at DESC
                LIMIT :limit OFFSET :offset;";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }

    public function count(): int
    {
        $sql = "SELECT COUNT(*) FROM transactions";
        $stmt = $this->db->query($sql);
        return (int)$stmt->fetchColumn();
    }

    public function find(int $id): ?Transaction 
    {
        $stmt = $this->db->prepare("SELECT * FROM transactions WHERE id = ?");
        $stmt->execute([$id]);
        $transaction = $stmt->fetch(PDO::FETCH_ASSOC);
        return $transaction ?: null;
    }

    public function create(Transaction $transaction): Transaction 
    {
        $stmt = $this->db->prepare("INSERT INTO transactions (user_id, product_id, quantity, total_price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$transaction->userId, $transaction->productId, $transaction->quantity, $transaction->totalAmount]);
        $transaction->id = (int)$this->db->lastInsertId();
        return $transaction;
    }
}
