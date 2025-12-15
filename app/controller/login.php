<?php
require_once __DIR__ . '/../bootstrap.php';

if (is_logged_in()) {
    redirect('app/controller/dashboard.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf(); // Check CSRF token
    
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = 'Both username and password are required.';
    } else {
        try {
            $pdo = Database::getInstance()->getConnection();
            $stmt = $pdo->prepare('SELECT id, password_hash, username, role FROM users WHERE username = ?');
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id']; 
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                redirect('app/controller/dashboard.php');
            } else {
                $error = 'Invalid username or password.';
            }
        } catch (PDOException $e) {
            error_log('Login DB Error: ' . $e->getMessage());
            $error = 'Internal error. Please try again later.';
        }
    }
}

require_once APP_ROOT . '/view/login.view.php';