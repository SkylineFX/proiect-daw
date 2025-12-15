<?php
require_once __DIR__ . '/../bootstrap.php';

// Unset all session variables
$_SESSION = [];

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        "",
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"],
    );
}

// Destroy the session data on the server
session_destroy();

// Redirect to the login page
redirect('app/controller/login.php');
