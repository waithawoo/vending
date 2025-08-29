<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/../config/session.php';

abstract class BaseController
{
    protected UserRepository $userRepository;
    protected int $sessionTimeout = 3600;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    protected function render(string $template, array $variables = [], int $statusCode = 200): Response
    {
        ob_start();
        extract($variables);
        $templatePath = __DIR__ . '/../views/' . $template;
        if (!file_exists($templatePath)) {
            throw new \RuntimeException("Template not found: {$template}");
        }
        include $templatePath;
        $content = ob_get_clean();
        return new Response($content, $statusCode);
    }

    protected function redirect(string $url, int $statusCode = 302): RedirectResponse
    {
        return new RedirectResponse($url, $statusCode);
    }

    protected function errors($errors, Request $request): array
    {
        return [
            'errors' => $errors,
            'data' => $request->request->all()
        ];
    }

    protected function requireAuth(): User
    {
        $user = $this->isAuthenticated();
        if (!$user) {
            throw new \RuntimeException('Authentication required', 401);
        }
        return $user;
    }

    protected function requireAdmin(): User
    {
        $user = $this->requireAuth();
        if (!$user->isAdmin()) {
            throw new \RuntimeException('Admin access required', 403);
        }
        return $user;
    }

    protected function isSessionSet(): bool
    {
        return isset($_SESSION['user_id'], $_SESSION['last_activity'])
            && ($_SESSION['last_activity'] + $this->sessionTimeout > time());
    }

    protected function isAuthenticated(): ?User
    {
        if (!$this->isSessionSet()) {
            return null;
        }

        $user = $this->userRepository->find($_SESSION['user_id']);

        if (!$user) {
            unset($_SESSION['user_id']);
            return null;
        }

        return $user;
    }

    protected function setFlashMessage(string $type, string $message): void
    {
        $_SESSION['flash'][$type] = $message;
    }

    protected function getFlashMessage(string $type): ?string
    {
        if (isset($_SESSION['flash'][$type])) {
            $message = $_SESSION['flash'][$type];
            unset($_SESSION['flash'][$type]);
            return $message;
        }
        return null;
    }
}
