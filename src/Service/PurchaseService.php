<?php

namespace App\Service;

use App\Entity\Product;
use App\Entity\Transaction;
use App\Entity\User;
use App\Repository\ProductRepository;
use App\Service\TransactionService;
use App\Service\ProductService;

class PurchaseService
{
    private ProductRepository $productRepository;
    private TransactionService $transactionService;
    private ProductService $productService;

    public function __construct(
        ProductRepository $productRepository,
        TransactionService $transactionService,
        ProductService $productService
    ) {
        $this->productRepository = $productRepository;
        $this->transactionService = $transactionService;
        $this->productService = $productService;
    }

    public function purchaseProduct(User $user, int $productId, int $quantity = 1): Transaction
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be positive');
        }

        $product = $this->productRepository->find($productId);
        if (!$product) {
            throw new \InvalidArgumentException('Product not found');
        }

        if (!$product->isAvailable()) {
            throw new \InvalidArgumentException('Product is out of stock');
        }

        if ($product->quantityAvailable < $quantity) {
            throw new \InvalidArgumentException('Insufficient quantity available');
        }

        $totalAmount = round($product->price * $quantity, 2);

        $product->decreaseQuantity($quantity);
        $this->productService->saveProduct($product);

        $transaction = new Transaction(
            $user->id,
            $productId,
            $quantity,
            $totalAmount
        );

        return $this->transactionService->createTransaction($transaction);
    }
}
