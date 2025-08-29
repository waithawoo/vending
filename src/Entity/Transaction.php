<?php

namespace App\Entity;

class Transaction
{
    public ?int $id;
    public int $userId;
    public int $productId;
    public int $quantity;
    public float $totalAmount;
    public ?\DateTime $transactionDate;

    public function __construct(
        int $userId,
        int $productId,
        int $quantity,
        float $totalAmount,
        ?int $id = null,
        ?\DateTime $transactionDate = null
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->totalAmount = $totalAmount;
        $this->transactionDate = $transactionDate ?? new \DateTime();
    }
}
