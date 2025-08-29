<?php

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use App\Service\PurchaseService;
use App\Service\ProductService;
use App\Service\TransactionService;
use App\Repository\ProductRepository;
use App\Entity\Product;
use App\Entity\User;
use App\Entity\Transaction;

class PurchaseServiceTest extends TestCase
{
    private PurchaseService $purchaseService;
    /** @var MockObject&ProductRepository */
    private MockObject $productRepository;
    /** @var MockObject&TransactionService */
    private MockObject $transactionService;
    /** @var MockObject&ProductService */
    private MockObject $productService;

    protected function setUp(): void
    {
        $this->productRepository = $this->createMock(ProductRepository::class);
        $this->transactionService = $this->createMock(TransactionService::class);
        $this->productService = $this->createMock(ProductService::class);
        $this->purchaseService = new PurchaseService(
            $this->productRepository,
            $this->transactionService,
            $this->productService
        );
    }

    public function testPurchaseProductSuccessfully(): void
    {
        $user = new User('testuser', 'test@example.com', 'password', 1);
        $product = new Product('Test Product', 19.99, 10, 1);
        
        $this->productRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($product);
            
        $this->productService->expects($this->once())
            ->method('saveProduct')
            ->with($product);

        $this->transactionService->expects($this->once())
            ->method('createTransaction')
            ->with($this->callback(function ($transaction) {
                return $transaction instanceof Transaction &&
                       $transaction->userId === 1 &&
                       $transaction->productId === 1 &&
                       $transaction->quantity === 5 &&
                       $transaction->totalAmount === 99.95;
            }))
            ->willReturn(new Transaction(1, 1, 5, 99.95));

        $result = $this->purchaseService->purchaseProduct($user, 1, 5);
        
        $this->assertInstanceOf(Transaction::class, $result);
        $this->assertEquals(5, $product->quantityAvailable);
    }

    public function testPurchaseProductNotFound(): void
    {
        $user = new User('testuser', 'test@example.com', 'password');
        
        $this->productRepository->expects($this->once())
            ->method('find')
            ->with(999)
            ->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Product not found');

        $this->purchaseService->purchaseProduct($user, 999, 1);
    }

    public function testPurchaseProductInsufficientStock(): void
    {
        $user = new User('testuser', 'test@example.com', 'password');
        $product = new Product('Test Product', 19.99, 3);
        
        $this->productRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($product);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Insufficient quantity available');

        $this->purchaseService->purchaseProduct($user, 1, 5);
    }

    public function testPurchaseProductWithZeroQuantity(): void
    {
        $user = new User('testuser', 'test@example.com', 'password');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Quantity must be positive');

        $this->purchaseService->purchaseProduct($user, 1, 0);
    }

    public function testPurchaseProductWithNegativeQuantity(): void
    {
        $user = new User('testuser', 'test@example.com', 'password');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Quantity must be positive');

        $this->purchaseService->purchaseProduct($user, 1, -1);
    }

    public function testPurchaseExactAvailableQuantity(): void
    {
        $user = new User('testuser', 'test@example.com', 'password', 1);
        $product = new Product('Test Product', 19.99, 5, 1);
        
        $this->productRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($product);

        $this->productService->expects($this->once())
            ->method('saveProduct')
            ->with($product);

        $this->transactionService->expects($this->once())
            ->method('createTransaction')
            ->willReturn(new Transaction(1, 1, 5, 99.95));

        $result = $this->purchaseService->purchaseProduct($user, 1, 5);
        
        $this->assertInstanceOf(Transaction::class, $result);
        $this->assertEquals(0, $product->quantityAvailable);
    }

    public function testPurchaseProductWithDefaultQuantity(): void
    {
        $user = new User('testuser', 'test@example.com', 'password', 1);
        $product = new Product('Test Product', 19.99, 10, 1);
        
        $this->productRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($product);

        $this->productService->expects($this->once())
            ->method('saveProduct')
            ->with($product);

        $this->transactionService->expects($this->once())
            ->method('createTransaction')
            ->willReturn(new Transaction(1, 1, 1, 19.99));

        $result = $this->purchaseService->purchaseProduct($user, 1);
        
        $this->assertInstanceOf(Transaction::class, $result);
        $this->assertEquals(9, $product->quantityAvailable);
    }
}
