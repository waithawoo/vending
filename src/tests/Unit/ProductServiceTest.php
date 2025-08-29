<?php

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use App\Service\ProductService;
use App\Repository\ProductRepository;
use App\Entity\Product;

class ProductServiceTest extends TestCase
{
    private ProductService $productService;
    /** @var MockObject&ProductRepository */
    private MockObject $productRepository;

    protected function setUp(): void
    {
        $this->productRepository = $this->createMock(ProductRepository::class);
        $this->productService = new ProductService($this->productRepository);
    }

    public function testPaginatedProductList(): void
    {
        $products = [new Product('Test Product', 19.99, 10)];
        
        $this->productRepository->expects($this->once())
            ->method('paginate')
            ->with(10, 0, 'name', 'ASC')
            ->willReturn($products);
            
        $this->productRepository->expects($this->once())
            ->method('count')
            ->willReturn(25);

        $result = $this->productService->paginatedProductList(1, 10, 'name', 'ASC');

        $this->assertEquals($products, $result['products']);
        $this->assertEquals(1, $result['currentPage']);
        $this->assertEquals(3, $result['totalPages']);
        $this->assertEquals(25, $result['totalProducts']);
    }

    public function testGetProduct(): void
    {
        $product = new Product('Test Product', 19.99, 10);
        
        $this->productRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($product);

        $result = $this->productService->getProduct(1);
        
        $this->assertEquals($product, $result);
    }

    public function testGetProductNotFound(): void
    {
        $this->productRepository->expects($this->once())
            ->method('find')
            ->with(999)
            ->willReturn(null);

        $result = $this->productService->getProduct(999);
        
        $this->assertNull($result);
    }

    public function testCreateProductWithArray(): void
    {
        $data = ['name' => 'New Product', 'price' => '29.99', 'quantity' => '5'];
        $product = new Product('New Product', 29.99, 5);
        
        $this->productRepository->expects($this->once())
            ->method('create')
            ->willReturn($product);

        $result = $this->productService->createProduct($data);
        
        $this->assertEquals($product, $result);
    }

    public function testCreateProductWithObject(): void
    {
        $product = new Product('Test Product', 19.99, 10);
        
        $this->productRepository->expects($this->once())
            ->method('create')
            ->with($product)
            ->willReturn($product);

        $result = $this->productService->createProduct($product);
        
        $this->assertEquals($product, $result);
    }

    public function testUpdateProduct(): void
    {
        $product = new Product('Old Name', 10.99, 5);
        $data = ['name' => 'New Name', 'price' => '19.99', 'quantity' => '10'];
        
        $this->productRepository->expects($this->once())
            ->method('update')
            ->with($product)
            ->willReturn(true);

        $result = $this->productService->updateProduct($product, $data);
        
        $this->assertTrue($result);
        $this->assertEquals('New Name', $product->name);
        $this->assertEquals(19.99, $product->price);
        $this->assertEquals(10, $product->quantityAvailable);
    }

    public function testSaveProduct(): void
    {
        $product = new Product('Test Product', 19.99, 10);
        
        $this->productRepository->expects($this->once())
            ->method('update')
            ->with($product)
            ->willReturn(true);

        $result = $this->productService->saveProduct($product);
        
        $this->assertTrue($result);
    }

    public function testDeleteProduct(): void
    {
        $this->productRepository->expects($this->once())
            ->method('delete')
            ->with(1)
            ->willReturn(true);

        $result = $this->productService->deleteProduct(1);
        
        $this->assertTrue($result);
    }
}
