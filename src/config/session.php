<?php

$sessionConfig = [
    'name'     => 'VENDING_SESS',
    'lifetime' => 3600,
    'path'     => '/',
    'domain'   => '',
    'secure'   => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Lax',
];

session_name($sessionConfig['name']);
session_set_cookie_params([
    'lifetime' => $sessionConfig['lifetime'],
    'path'     => $sessionConfig['path'],
    'domain'   => $sessionConfig['domain'],
    'secure'   => $sessionConfig['secure'],
    'httponly' => $sessionConfig['httponly'],
    'samesite' => $sessionConfig['samesite'],
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$timeout = $sessionConfig['lifetime'];
if (isset($_SESSION['last_activity']) && ($_SESSION['last_activity'] + $timeout) < time()) {
    session_unset();
    session_destroy();
    session_start();
}
$_SESSION['last_activity'] = time();
