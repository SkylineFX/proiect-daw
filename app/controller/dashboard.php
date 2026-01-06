<?php

require_once __DIR__ . '/../bootstrap.php';

// Redirect unauthenticated users to the login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Handle Order Actions (POST)
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Only allow logged in users (already checked above)
    // verify_csrf(); // Ideally we should verify CSRF here too if we had the token in the form

    if (isset($_POST['action']) && isset($_POST['order_id'])) {
        $action = $_POST['action'];
        $orderId = (int)$_POST['order_id'];

        try {
            $pdo = Database::getInstance()->getConnection();

            if ($action === 'cancel') {
                // Client can cancel if pending. Admin can always cancel.
                if (is_admin()) {
                    $stmt = $pdo->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ?");
                    $stmt->execute([$orderId]);
                    $success = "Order #$orderId status updated to Cancelled.";
                } else {
                    $stmt = $pdo->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ? AND user_id = ? AND status = 'pending'");
                    $stmt->execute([$orderId, $_SESSION['user_id']]);
                    if ($stmt->rowCount() > 0) {
                        $success = "Order #$orderId has been cancelled.";
                    } else {
                        $error = "Unable to cancel order. It may not be pending or does not belong to you.";
                    }
                }
            } elseif ($action === 'ship' && is_admin()) {
                $stmt = $pdo->prepare("UPDATE orders SET status = 'shipped' WHERE id = ?");
                $stmt->execute([$orderId]);
                $success = "Order #$orderId marked as Shipped.";
            }

        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    }
}

// Fetch User Data
try {
    $pdo = Database::getInstance()->getConnection();
    
    // 1. Profile Info
    $stmt = $pdo->prepare('SELECT created_at FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $userRow = $stmt->fetch();
    $register_date = $userRow ? $userRow['created_at'] : 'N/A';

    // 2. Fetch Orders
    $orders = [];
    if (is_admin()) {
        // Admin sees all orders
        $stmt = $pdo->query("
            SELECT o.*, u.username, 
                   (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
            FROM orders o 
            JOIN users u ON o.user_id = u.id 
            ORDER BY o.created_at DESC
        ");
    } else {
        // Client sees own orders
        $stmt = $pdo->prepare("
            SELECT o.*, 
                   (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
            FROM orders o 
            WHERE o.user_id = ? 
            ORDER BY o.created_at DESC
        ");
        $stmt->execute([$_SESSION['user_id']]);
    }
    $orders = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log('Dashboard DB Error: ' . $e->getMessage());
    $error = "Could not load dashboard data.";
}

require_once APP_ROOT . '/view/dashboard.view.php';
?>