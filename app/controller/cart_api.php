<?php
require_once __DIR__ . '/../bootstrap.php';

header('Content-Type: application/json');

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$productId = $input['product_id'] ?? 0;
$quantity = $input['quantity'] ?? 1;

if (!$productId && $action !== 'count') {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit;
}

$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$db = $userId ? Database::getInstance()->getConnection() : null;

switch ($action) {
    case 'add':
        // Session
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = $quantity;
        }
        // DB
        if ($userId) {
            $newQty = $_SESSION['cart'][$productId];
            $stmt = $db->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = ?");
            $stmt->execute([$userId, $productId, $newQty, $newQty]);
        }
        break;

    case 'remove':
        // Session
        unset($_SESSION['cart'][$productId]);
        // DB
        if ($userId) {
            $stmt = $db->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$userId, $productId]);
        }
        break;

    case 'update':
        // Session
        if ($quantity > 0) {
            $_SESSION['cart'][$productId] = $quantity;
            // DB
            if ($userId) {
                $stmt = $db->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = ?");
                $stmt->execute([$userId, $productId, $quantity, $quantity]);
            }
        } else {
            unset($_SESSION['cart'][$productId]);
            // DB
            if ($userId) {
                $stmt = $db->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_id = ?");
                $stmt->execute([$userId, $productId]);
            }
        }
        break;
        
    case 'clear':
        $_SESSION['cart'] = [];
        if ($userId) {
            $stmt = $db->prepare("DELETE FROM cart_items WHERE user_id = ?");
            $stmt->execute([$userId]);
        }
        break;
}

// Calculate total items
$totalItems = array_sum($_SESSION['cart']);

echo json_encode([
    'success' => true, 
    'count' => $totalItems,
    'cart' => $_SESSION['cart']
]);
