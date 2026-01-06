<?php
require_once __DIR__ . '/../bootstrap.php';

if (is_logged_in()) {
    redirect('app/controller/dashboard.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf(); // Check CSRF token

    // Verify reCAPTCHA
    if (!verify_recaptcha($_POST['g-recaptcha-response'] ?? '')) {
        $error = 'Please complete the reCAPTCHA verification.';
    } else {
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
                $newUserId = $pdo->lastInsertId();
                
                // Auto-login
                $_SESSION['user_id'] = $newUserId;
                $_SESSION['username'] = $username;
                $_SESSION['role'] = 'client'; // Default role

                // Sync Cart: Save session cart to DB for the new user
                if (!empty($_SESSION['cart'])) {
                     $insertSql = "INSERT INTO cart_items (user_id, product_id, quantity) VALUES ";
                     $insertParams = [];
                     $placeholders = [];
                     foreach ($_SESSION['cart'] as $pid => $qty) {
                         $placeholders[] = "(?, ?, ?)";
                         $insertParams[] = $newUserId;
                         $insertParams[] = $pid;
                         $insertParams[] = $qty;
                     }
                     if (!empty($placeholders)) {
                         $pdo->prepare($insertSql . implode(', ', $placeholders))->execute($insertParams);
                     }
                }
                
                $_SESSION['flash_success'] = 'Registration successful! You are now logged in.';

                redirect('index.php');
            }
        } catch (PDOException $e) {
            error_log('Database error (register): ' . $e->getMessage());
            $error = 'An internal error occurred. Please try again later.';
        }
    }
    }
}

require_once APP_ROOT . '/view/register.view.php';