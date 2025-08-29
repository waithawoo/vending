<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Service\ProductService;
use App\Service\PurchaseService;
use App\Repository\UserRepository;

class ProductController extends BaseController
{
    private ProductService $productService;
    private PurchaseService $purchaseService;

    public function __construct(UserRepository $userRepository, ProductService $productService, PurchaseService $purchaseService) 
    {
        parent::__construct($userRepository);
        $this->purchaseService = $purchaseService;
        $this->productService = $productService;
    }

    public function index(Request $request): Response
    {
        $viewToRender = 'products/public.php';
        $authenticatedUser = $this->isAuthenticated();
        
        if ($authenticatedUser && $authenticatedUser->isAdmin()) {
            $viewToRender = 'products/index.php';
        }

        $page = max(1, (int) $request->query->get('page', 1));
        $limit = min(50, max(5, (int) $request->query->get('limit', 10)));
        $sortBy = $request->query->get('sort', 'name');
        $sortDirection = $request->query->get('direction', 'ASC');

        $data = $this->productService->paginatedProductList($page, $limit, $sortBy, $sortDirection);
        return $this->render($viewToRender, $data);
    }

    public function create(Request $request): Response
    {
        $this->requireAdmin();

        if ($request->getMethod() === 'POST') {
            $errors = $this->validateProductData($request);
            if (!empty($errors)) {
                return $this->render('products/create.php', $this->errors($errors, $request));
            }
            $this->productService->createProduct($request->request->all());
            $this->setFlashMessage('success', 'Product created successfully');
            return $this->redirect('/products');
        }

        return $this->render('products/create.php');
    }

    public function update(Request $request, array $args): Response
    {
        $this->requireAdmin();

        $id = (int) $args['id'];
        $product = $this->productService->getProduct($id);

        if (!$product) {
            throw new \Exception('No Product data', 404);
        }

        if ($request->getMethod() === 'POST') {
            $errors = $this->validateProductData($request);
            if (!empty($errors)) {
                return $this->render('products/edit.php', $this->errors($errors, $request));
            }
            $this->productService->updateProduct($product, $request->request->all());
            $this->setFlashMessage('success', 'Product updated successfully');
            return $this->redirect('/products');
        }

        return $this->render('products/edit.php', ['product' => (array) $product]);
    }

    public function delete(Request $request, array $args): Response
    {
        $this->requireAdmin();

        $id = (int) $args['id'];
        $product = $this->productService->getProduct($id);
        if (!$product) {
            throw new \Exception('No Product data', 404);
        }
        $this->productService->deleteProduct($id);
        $this->setFlashMessage('success', 'Product deleted successfully');
        return $this->redirect('/products');
    }

    public function purchase(Request $request): Response
    {
        $user = $this->requireAuth();

        $productId = $request->request->get('product_id');
        $quantity = max(1, (int) $request->request->get('quantity', 1));
        
        $this->purchaseService->purchaseProduct($user, $productId, $quantity);
        $this->setFlashMessage('success', 'Product purchased successfully');
        return $this->redirect('/products');
    }

    private function validateProductData(Request $request): array
    {
        $errors = [];
        
        $name = trim($request->request->get('name', ''));
        $price = $request->request->get('price', '');
        $quantity = $request->request->get('quantity', '');

        if (empty($name)) {
            $errors[] = 'Name is required';
        }

        if (empty($price) || !is_numeric($price) || (float) $price <= 0) {
            $errors[] = 'Price must be a positive number';
        }

        if (empty($quantity) || !is_numeric($quantity) || (int) $quantity < 0) {
            $errors[] = 'Quantity must be a non-negative integer';
        }

        return $errors;
    }
}
