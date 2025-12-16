<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../model/Product.php';

// Get cart items from session
$cart = $_SESSION['cart'] ?? [];
$cartItems = [];
$totalPrice = 0;

$productModel = new Product();

if (!empty($cart)) {
    foreach ($cart as $id => $qty) {
        $product = $productModel->getProductById($id);
        if ($product) {
            $product['qty'] = $qty;
            $product['line_total'] = $product['price'] * $qty;
            $totalPrice += $product['line_total'];
            $cartItems[] = $product;
        }
    }
}

require_once APP_ROOT . '/view/cart.view.php';
