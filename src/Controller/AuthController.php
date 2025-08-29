<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\AuthService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends BaseController
{
    private AuthService $authService;

    public function __construct(UserRepository $userRepository, AuthService $authService)
    {
        parent::__construct($userRepository);
        $this->authService = $authService;
    }

    public function login(Request $request): Response
    {
        if ($this->isAuthenticated()) {
            return $this->redirect('/products');
        }

        if ($request->getMethod() === 'POST') {
            $email = trim($request->request->get('email', ''));
            $password = $request->request->get('password', '');

            $errors = [];
            if (empty($email)) {
                $errors[] = 'Email is required';
            }
            if (empty($password)) {
                $errors[] = 'Password is required';
            }

            if (empty($errors)) {
                $user = $this->authService->authenticate($email, $password);
                if ($user) {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $user->id;
                    $_SESSION['role'] = $user->role;
                    $_SESSION['last_activity'] = time();
                    $this->setFlashMessage('success', 'Login successful');
                    return $this->redirect('/products');
                } else {
                    $errors[] = 'Invalid username or password';
                }
            }

            return $this->render('auth/login.php', [
                'errors' => $errors,
                'email' => $email
            ]);
        }

        return $this->render('auth/login.php');
    }

    public function logout(Request $request): Response
    {
        session_destroy();
        return $this->redirect('/login');
    }

    public function register(Request $request): Response
    {
        if ($this->isAuthenticated()) {
            return $this->redirect('/products');
        }

        if ($request->getMethod() === 'POST') {
            $email = trim($request->request->get('email', ''));
            $password = $request->request->get('password', '');
            $confirmPassword = $request->request->get('confirm_password', '');

            $errors = $this->validateRegistrationData($email, $password, $confirmPassword);

            if (empty($errors)) {
                try {
                    $user = $this->authService->register($email, $password);
                    $this->setFlashMessage('success', 'Registration successful. Please login.');
                    return $this->redirect('/login');
                } catch (\Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }
            return $this->render('auth/register.php', [
                'errors' => $errors,
                'email' => $email
            ]);
        }

        return $this->render('auth/register.php');
    }

    private function validateRegistrationData(string $email, string $password, string $confirmPassword): array
    {
        $errors = [];

        if (empty($email)) {
            $errors[] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }

        if (empty($password)) {
            $errors[] = 'Password is required';
        } elseif (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters long';
        }

        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match';
        }

        return $errors;
    }
}
