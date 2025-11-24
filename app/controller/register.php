<?php

require_once '../model/Database.php';
require_once '../model/Validator.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

try {
    $pdo = Database::getInstance()->getConnection();
} catch (PDOException $e) {
    error_log('Database connection failed: ' . $e->getMessage());
    $error = 'Internal error. Please try again later.';
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['user_id'])) {
        header('Location: dashboard.php');
        exit();
    }

    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = 'Invalid form submission.';
        exit();
    }

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $validationErrors = Validator::validateRegistration($username, $email, $password);
    if ($validationErrors) {
        $error = $validationErrors[0]; //$error = implode('<br>', $validationErrors);
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
            $stmt->execute([$username, $email]);
            
            if ($stmt->rowCount() > 0) {
                $error = 'Username or email already taken.';
            } else {
                $stmt = $pdo->prepare('INSERT INTO users (username, password_hash, email) VALUES (?, ?, ?)');
                $stmt->execute([$username, $password_hash, $email]);
                
                $_SESSION['flash_success'] = 'Registration successful! You can now log in.';
                unset($_SESSION['csrf_token']); 

                header('Location: login.php'); 
                exit();
            }
        } catch (PDOException $e) {
            error_log('Database error (register): ' . $e->getMessage());
            $error = 'An internal error occurred. Please try again later.';
        }
    }
}

require_once '../../view/register.view.php'
?>