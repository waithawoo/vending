<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Service\TransactionService;
use App\Repository\UserRepository;

class TransactionController extends BaseController
{
    private TransactionService $transactionService;

    public function __construct(UserRepository $userRepository,TransactionService $transactionService) 
    {
        parent::__construct($userRepository);
        $this->transactionService = $transactionService;
    }

    public function index(Request $request): Response
    {
        $this->requireAdmin();
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = min(50, max(5, (int) $request->query->get('limit', 10)));

        $data = $this->transactionService->paginatedProductList($page, $limit);
        return $this->render('transactions/index.php', $data);
    }
}
