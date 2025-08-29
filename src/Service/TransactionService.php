<?php

namespace App\Service;

use App\Repository\TransactionRepository;
use App\Entity\Transaction;

class TransactionService
{
    private TransactionRepository $transactionRepository;

    public function __construct() 
    {
        $this->transactionRepository = new TransactionRepository();
    }

    public function paginatedProductList($page, $limit): array 
    {
        $offset = ($page - 1) * $limit;
        $transactions = $this->transactionRepository->paginate($limit, $offset);
        $totalTransactions = $this->transactionRepository->count();
        $totalPages = ceil($totalTransactions / $limit);

        return [
            'transactions' => $transactions,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'limit' => $limit,
            'totalTransactions' => $totalTransactions
        ];
    }

    public function getTransaction(int $id): Transaction 
    {
        return $this->transactionRepository->find($id);
    }

    public function createTransaction(array|Transaction $data): Transaction 
    {
        if ($data instanceof Transaction) {
            $transaction = $data;
        } else {
            $transaction = new Transaction(
                (int) $data['user_id'],
                (int) $data['product_id'],
                (int) $data['quantity'],
                (float) $data['total_price']
            );
        }
        return $this->transactionRepository->create($transaction);
    }
}
