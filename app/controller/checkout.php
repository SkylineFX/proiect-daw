<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../model/Product.php';

require_login();

// Redirect if cart is empty
if (empty($_SESSION['cart'])) {
    redirect('app/controller/cart.php');
}

$user = [
    'id' => $_SESSION['user_id'],
    'username' => $_SESSION['username'],
    // We would fetch email from DB if needed for mail(), let's assume we can get it or just rely on session if we stored it
];

// Fetch user email for confirmation
$pdo = Database::getInstance()->getConnection();
$stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
$stmt->execute([$user['id']]);
$userEmail = $stmt->fetchColumn();

// Calculate Cart Total
$cart = $_SESSION['cart'];
$productModel = new Product();
$cartItems = [];
$cartTotal = 0;

foreach ($cart as $id => $qty) {
    // Fetch fresh price
    $product = $productModel->getProductById($id);
    if ($product) {
        $product['qty'] = $qty;
        $product['line_total'] = $product['price'] * $qty;
        $cartTotal += $product['line_total'];
        $cartItems[] = $product;
    }
}

$error = '';

// Handle POST (Place Order)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $addressSelection = $_POST['address_selection'] ?? 'new';
    $finalAddressId = null;
    $finalAddressString = ''; // For email

    try {
        $pdo->beginTransaction();

        // Validate Stock
        foreach ($cartItems as $item) {
            if ($item['stock'] < $item['qty']) {
                throw new Exception("Stoc insuficient pentru produsul: " . $item['name'] . " (Disponibil: " . $item['stock'] . ")");
            }
        }

        if ($addressSelection === 'new') {
            // Validate new address
            $city = sanitize($_POST['city'] ?? '');
            $postalCode = sanitize($_POST['postal_code'] ?? '');
            $addressLine = sanitize($_POST['address_line'] ?? '');

            if (empty($city) || empty($addressLine) || empty($postalCode)) {
                throw new Exception("Please fill in all address fields.");
            }

            $shouldSave = isset($_POST['save_address']);

            if ($shouldSave) {
                $stmt = $pdo->prepare("INSERT INTO addresses (user_id, city, postal_code, address_line) VALUES (?, ?, ?, ?)");
                $stmt->execute([$user['id'], $city, $postalCode, $addressLine]);
                $finalAddressId = $pdo->lastInsertId();
            }
            
            $finalAddressString = "$city, $addressLine ($postalCode)";

        } else {
            // Validate existing address ownership
            $addrId = (int)$addressSelection;
            $stmt = $pdo->prepare("SELECT * FROM addresses WHERE id = ? AND user_id = ?");
            $stmt->execute([$addrId, $user['id']]);
            $existingAddr = $stmt->fetch();

            if (!$existingAddr) {
                throw new Exception("Invalid address selected.");
            }
            $finalAddressId = $existingAddr['id']; // We don't really link order -> address ID in simplistic schema, but we could.
            // Schema has no address_id in orders, normally we snapshot the address into the order.
            // But for this request "send confirmation mail", we just need it for that.
            $finalAddressString = $existingAddr['city'] . ', ' . $existingAddr['address_line'] . ' (' . $existingAddr['postal_code'] . ')';
        }

        // Create Order
        $stmtEntry = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status, delivery_address) VALUES (?, ?, 'pending', ?)");
        $stmtEntry->execute([$user['id'], $cartTotal, $finalAddressString]);
        $orderId = $pdo->lastInsertId();

        // Create Order Items and Update Stock
        $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)");
        $stmtStock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        
        foreach ($cartItems as $item) {
            $stmtItem->execute([$orderId, $item['id'], $item['qty'], $item['price']]);
            $stmtStock->execute([$item['qty'], $item['id']]);
        }

        // Clear Cart
        $_SESSION['cart'] = [];
        $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?")->execute([$user['id']]);

        $pdo->commit();

        // Send Email via PHPMailer
        require_once APP_ROOT . '/app/model/MailService.php';
        
        // Items array for mail format
        $mailItems = $cartItems;
        
        MailService::sendOrderConfirmation(
            $userEmail, 
            $user['username'], 
            $orderId, 
            $cartTotal, 
            $finalAddressString, 
            $mailItems
        );

        // Flash success and redirect
        // Ideally redirect to a "Order Success" page, but Dashboard is fine for now
        $_SESSION['flash_success'] = "Comanda #$orderId a fost plasata cu succes! Un email de confirmare a fost trimis.";
        redirect('app/controller/dashboard.php');

    } catch (Exception $e) {
        $pdo->rollBack();
        $error = $e->getMessage();
    }
}

// Get Saved Addresses for View
$stmtAddr = $pdo->prepare("SELECT * FROM addresses WHERE user_id = ? ORDER BY id DESC");
$stmtAddr->execute([$user['id']]);
$savedAddresses = $stmtAddr->fetchAll();

require_once APP_ROOT . '/view/checkout.view.php';
