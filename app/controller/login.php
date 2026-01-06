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

                // --- Cart Sync Logic ---
                if (!isset($_SESSION['cart'])) {
                    $_SESSION['cart'] = [];
                }

                // 1. Fetch DB Cart
                $stmtCart = $pdo->prepare("SELECT product_id, quantity FROM cart_items WHERE user_id = ?");
                $stmtCart->execute([$user['id']]);
                $dbCartItems = $stmtCart->fetchAll(PDO::FETCH_KEY_PAIR); // [product_id => quantity]

                // 2. Merge DB Cart into Session Cart
                foreach ($dbCartItems as $pid => $qty) {
                    if (isset($_SESSION['cart'][$pid])) {
                        $_SESSION['cart'][$pid] += $qty;
                    } else {
                        $_SESSION['cart'][$pid] = $qty;
                    }
                }

                // 3. Save Merged Cart back to DB
                // Clear old DB cart for this user to avoid complexity, then re-insert all
                $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?")->execute([$user['id']]);
                
                if (!empty($_SESSION['cart'])) {
                    $insertSql = "INSERT INTO cart_items (user_id, product_id, quantity) VALUES ";
                    $insertParams = [];
                    $placeholders = [];
                    foreach ($_SESSION['cart'] as $pid => $qty) {
                        $placeholders[] = "(?, ?, ?)";
                        $insertParams[] = $user['id'];
                        $insertParams[] = $pid;
                        $insertParams[] = $qty;
                    }
                    if (!empty($placeholders)) {
                        $pdo->prepare($insertSql . implode(', ', $placeholders))->execute($insertParams);
                    }
                }
                // --- End Cart Sync ---

                redirect('/index.php');
            } else {
                $error = 'Invalid username or password.';
            }
        } catch (PDOException $e) {
            error_log('Login DB Error: ' . $e->getMessage());
            $error = 'Internal error. Please try again later.';
        }
    }
    }
}

require_once APP_ROOT . '/view/login.view.php';