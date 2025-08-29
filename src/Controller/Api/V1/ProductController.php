<?php

namespace App\Controller\Api\V1;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\ProductService;
use App\Service\PurchaseService;
use App\Repository\UserRepository;

class ProductController extends ApiController
{
    private ProductService $productService;
    private PurchaseService $purchaseService;

    public function __construct(UserRepository $userRepository, ProductService $productService, PurchaseService $purchaseService) 
    {
        parent::__construct($userRepository);
        $this->productService = $productService;
        $this->purchaseService = $purchaseService;
    }

    public function index(Request $request): JsonResponse
    {
        $this->requireAuth($request);
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = min(50, max(5, (int) $request->query->get('limit', 10)));
        $sortBy = $request->query->get('sort', 'name');
        $sortDirection = $request->query->get('direction', 'ASC');

        $data = $this->productService->paginatedProductList($page, $limit, $sortBy, $sortDirection);
        return new JsonResponse($data);
    }

    public function purchase(Request $request): JsonResponse
    {
        $user = $this->requireAuth($request);
        if ($user->isAdmin()) {
            return new JsonResponse(['message' => 'Admins cannot purchase products'], 403);
        }
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['product_id']) || !is_int($data['product_id']) || $data['product_id'] < 1) {
            throw new \InvalidArgumentException('Invalid product_id');
        }
        if (!isset($data['quantity']) || !is_int($data['quantity']) || $data['quantity'] < 1) {
            throw new \InvalidArgumentException('Invalid quantity');
        }

        $this->purchaseService->purchaseProduct($user, $data['product_id'], $data['quantity']);
        return new JsonResponse(['message' => 'Purchase successful']);
    }
}
