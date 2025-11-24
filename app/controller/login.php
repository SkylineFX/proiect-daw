<?php

require '../model/Database.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
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
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = 'Both username and password are required.';
    } else {
        try {
            $stmt = $pdo->prepare('SELECT id, password_hash, username, role FROM users WHERE username = ?');
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id']; 
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Invalid username or password.';
            }
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            $error = 'Internal error. Please try again later.';
        }
    }
}

require_once '../../view/login.view.php'
?>