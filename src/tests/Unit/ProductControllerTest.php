<?php

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\ProductController;
use App\Service\ProductService;
use App\Service\PurchaseService;
use App\Repository\UserRepository;
use App\Entity\Product;

class ProductControllerTest extends TestCase
{
    private ProductController $controller;
    /** @var MockObject&ProductService */
    private MockObject $productService;
    /** @var MockObject&PurchaseService */
    private MockObject $purchaseService;
    /** @var MockObject&UserRepository */
    private MockObject $userRepository;

    protected function setUp(): void
    {
        $this->productService = $this->createMock(ProductService::class);
        $this->purchaseService = $this->createMock(PurchaseService::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->controller = new ProductController($this->userRepository, $this->productService, $this->purchaseService);
    }

    public function testValidateProductDataWithValidData(): void
    {
        $request = Request::create('/products', 'POST', [
            'name' => 'Valid Product',
            'price' => '19.99',
            'quantity' => '10'
        ]);

        $reflection = new \ReflectionClass($this->controller);
        $method = $reflection->getMethod('validateProductData');
        $method->setAccessible(true);

        $errors = $method->invoke($this->controller, $request);
        $this->assertEmpty($errors);
    }

    public function testValidateProductDataWithEmptyName(): void
    {
        $request = Request::create('/products', 'POST', [
            'name' => '',
            'price' => '19.99',
            'quantity' => '10'
        ]);

        $reflection = new \ReflectionClass($this->controller);
        $method = $reflection->getMethod('validateProductData');
        $method->setAccessible(true);

        $errors = $method->invoke($this->controller, $request);
        $this->assertContains('Name is required', $errors);
    }

    public function testValidateProductDataWithInvalidPrice(): void
    {
        $request = Request::create('/products', 'POST', [
            'name' => 'Test Product',
            'price' => '-5.99',
            'quantity' => '10'
        ]);

        $reflection = new \ReflectionClass($this->controller);
        $method = $reflection->getMethod('validateProductData');
        $method->setAccessible(true);

        $errors = $method->invoke($this->controller, $request);
        $this->assertContains('Price must be a positive number', $errors);
    }

    public function testValidateProductDataWithInvalidQuantity(): void
    {
        $request = Request::create('/products', 'POST', [
            'name' => 'Test Product',
            'price' => '19.99',
            'quantity' => '-5'
        ]);

        $reflection = new \ReflectionClass($this->controller);
        $method = $reflection->getMethod('validateProductData');
        $method->setAccessible(true);

        $errors = $method->invoke($this->controller, $request);
        $this->assertContains('Quantity must be a non-negative integer', $errors);
    }

    public function testValidateProductDataWithMultipleErrors(): void
    {
        $request = Request::create('/products', 'POST', [
            'name' => '',
            'price' => 'invalid',
            'quantity' => 'invalid'
        ]);

        $reflection = new \ReflectionClass($this->controller);
        $method = $reflection->getMethod('validateProductData');
        $method->setAccessible(true);

        $errors = $method->invoke($this->controller, $request);
        $this->assertCount(3, $errors);
        $this->assertContains('Name is required', $errors);
        $this->assertContains('Price must be a positive number', $errors);
        $this->assertContains('Quantity must be a non-negative integer', $errors);
    }

    public function testValidateProductDataWithZeroQuantity(): void
    {
        $request = Request::create('/products', 'POST', [
            'name' => 'Test Product',
            'price' => '19.99',
            'quantity' => '0'
        ]);

        $reflection = new \ReflectionClass($this->controller);
        $method = $reflection->getMethod('validateProductData');
        $method->setAccessible(true);

        $errors = $method->invoke($this->controller, $request);
        $this->assertContains('Quantity must be a non-negative integer', $errors);
    }

    public function testCreateProductCallsService(): void
    {
        $data = ['name' => 'Test', 'price' => '10.99', 'quantity' => '5'];
        $product = new Product('Test', 10.99, 5);

        $this->productService->expects($this->once())
            ->method('createProduct')
            ->with($data)
            ->willReturn($product);

        $result = $this->productService->createProduct($data);
        $this->assertEquals($product, $result);
    }

    public function testGetProductCallsService(): void
    {
        $product = new Product('Test Product', 19.99, 10);

        $this->productService->expects($this->once())
            ->method('getProduct')
            ->with(1)
            ->willReturn($product);

        $result = $this->productService->getProduct(1);
        $this->assertEquals($product, $result);
    }

    public function testDeleteProductCallsService(): void
    {
        $this->productService->expects($this->once())
            ->method('deleteProduct')
            ->with(1)
            ->willReturn(true);

        $result = $this->productService->deleteProduct(1);
        $this->assertTrue($result);
    }
}
