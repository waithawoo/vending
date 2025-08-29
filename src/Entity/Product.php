<?php

namespace App\Entity;

class Product
{
    public ?int $id;
    public string $name;
    public float $price;
    public int $quantityAvailable;
    public ?\DateTime $createdAt;
    public ?\DateTime $updatedAt;

    public function __construct(
        string $name,
        float $price,
        int $quantityAvailable,
        ?int $id = null,
        ?\DateTime $createdAt = null,
        ?\DateTime $updatedAt = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->quantityAvailable = $quantityAvailable;
        $this->createdAt = $createdAt ?? new \DateTime();
        $this->updatedAt = $updatedAt ?? new \DateTime();
    }

    public function decreaseQuantity(int $amount): void
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be positive');
        }
        if ($this->quantityAvailable < $amount) {
            throw new \InvalidArgumentException('Insufficient quantity available');
        }
        $this->quantityAvailable -= $amount;
        $this->updatedAt = new \DateTime();
    }

    public function isAvailable(): bool
    {
        return $this->quantityAvailable > 0;
    }
}
