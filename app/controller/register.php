<?php
require_once __DIR__ . '/../bootstrap.php';

if (is_logged_in()) {
    redirect('app/controller/dashboard.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf(); // Check CSRF token

    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    // Validator already included via bootstrap
    $validationErrors = Validator::validateRegistration($username, $email, $password);
    if ($validationErrors) {
        $error = $validationErrors[0]; 
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $pdo = Database::getInstance()->getConnection();
            $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
            $stmt->execute([$username, $email]);
            
            if ($stmt->rowCount() > 0) {
                $error = 'Username or email already taken.';
            } else {
                $stmt = $pdo->prepare('INSERT INTO users (username, password_hash, email) VALUES (?, ?, ?)');
                $stmt->execute([$username, $password_hash, $email]);
                
                $_SESSION['flash_success'] = 'Registration successful! You can now log in.';
                // unset($_SESSION['csrf_token']); // Optional: Keep token valid for multiple requests or regenerate

                redirect('app/controller/login.php');
            }
        } catch (PDOException $e) {
            error_log('Database error (register): ' . $e->getMessage());
            $error = 'An internal error occurred. Please try again later.';
        }
    }
}

require_once APP_ROOT . '/view/register.view.php';