<?php
require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use DI\ContainerBuilder;

use App\Core\Router;

use App\Controller\AuthController;
use App\Controller\ProductController;
use App\Controller\TransactionController;
use App\Controller\Api\V1\AuthController as ApiAuthController;
use App\Controller\Api\V1\ProductController as ApiProductController;

$containerBuilder = new ContainerBuilder();
$container = $containerBuilder->build();

$router = new Router($container);
$request = Request::createFromGlobals();

// Auth routes
$router->add('register', '/register', [AuthController::class, 'register'], ['GET', 'POST']);
$router->add('login', '/login', [AuthController::class, 'login'], ['GET', 'POST']);
$router->add('logout', '/logout', [AuthController::class, 'logout'], ['GET']);

// Product routes
$router->add('products_list', '/products', [ProductController::class, 'index'], ['GET']);
$router->add('products_create', '/products/create', [ProductController::class, 'create'], ['GET', 'POST']);
$router->add('products_update', '/products/{id}/update', [ProductController::class, 'update'], ['GET', 'POST']);
$router->add('products_delete', '/products/{id}/delete', [ProductController::class, 'delete'], ['GET']);
$router->add('products_purchase', '/products/purchase', [ProductController::class, 'purchase'], ['POST']);
$router->add('transactions', '/transactions', [TransactionController::class, 'index'], ['GET']);

// Api routes
$router->add('api_register', '/api/v1/register', [ApiAuthController::class, 'register'], ['POST']);
$router->add('api_login', '/api/v1/login', [ApiAuthController::class, 'login'], ['POST']);
$router->add('api_products', '/api/v1/products', [ApiProductController::class, 'index'], ['GET']);
$router->add('api_products_purchase', '/api/v1/products/purchase', [ApiProductController::class, 'purchase'], ['POST']);

if ($request->getPathInfo() === '/') {
    $response = new RedirectResponse('/products');
    $response->send();
    exit;
}

$response = $router->dispatch($request);
$response->send();
