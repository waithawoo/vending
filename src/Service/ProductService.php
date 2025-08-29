<?php

namespace App\Service;

use App\Repository\ProductRepository;
use App\Entity\Product;

class ProductService
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository) 
    {
        $this->productRepository = $productRepository;
    }

    public function paginatedProductList($page, $limit, $sortBy, $sortDirection): array 
    {
        $offset = ($page - 1) * $limit;
        $products = $this->productRepository->paginate($limit, $offset, $sortBy, $sortDirection);
        $totalProducts = $this->productRepository->count();
        $totalPages = ceil($totalProducts / $limit);

        return [
            'products' => $products,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'limit' => $limit,
            'sortBy' => $sortBy,
            'sortDirection' => $sortDirection,
            'totalProducts' => $totalProducts
        ];
    }

    public function getProduct(int $id): ?Product 
    {
        return $this->productRepository->find($id);
    }

    public function createProduct(array|Product $data): Product 
    {
        if ($data instanceof Product) {
            $product = $data;
        } else {
            $product = new Product($data['name'], (float) $data['price'], (int) $data['quantity']);
        }
        return $this->productRepository->create($product);
    }

    public function saveProduct(Product $product): bool
    {
        return $this->productRepository->update($product);
    }

    public function updateProduct(Product $product, array $data): bool 
    {
        if (isset($data['name'])) {
            $product->name = $data['name'];
        }
        if (isset($data['price'])) {
            $product->price = (float) $data['price'];
        }
        if (isset($data['quantity'])) {
            $product->quantityAvailable = (int) $data['quantity'];
        }

        return $this->productRepository->update($product);
    }

    public function deleteProduct(int $id): bool 
    {
        return $this->productRepository->delete($id);
    }
}
