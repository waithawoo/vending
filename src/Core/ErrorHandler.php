<?php

namespace App\Core;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

ini_set('error_log', __DIR__ . '/../../php_errors.log');

class ErrorHandler
{
    public static function handle(\Throwable $e, Request $request): Response
    {
        error_log($e->getMessage());

        $status = 500;
        if ($e->getCode() >= 400 && $e->getCode() < 600) {
            $status = $e->getCode();
        } elseif ($e instanceof \InvalidArgumentException) {
            $status = 422;
        }

        $message = $status === 500 ? 'Internal Server Error' : $e->getMessage();

        $isApi = str_starts_with($request->getPathInfo(), '/api') || $request->getAcceptableContentTypes()[0] === 'application/json';

        if (!$isApi) {
            ob_start();
            extract([$message, $status]);
            include __DIR__ . '/../views/errors/err.php';
            $content = ob_get_clean();

            return new Response($content, $status);
        }

        return new JsonResponse(['message' => $message], $status);
    }
}
