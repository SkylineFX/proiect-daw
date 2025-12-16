<?php
require_once __DIR__ . '/app/bootstrap.php';
require_once __DIR__ . '/app/model/Product.php';

$productModel = new Product();
$products = $productModel->getAllProducts();
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Magazin Online - Papetarie</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="assets/cart.js"></script>
    <link rel="stylesheet" href="assets/style.css">

</head>
<body>

    <header>
        <a href="index.php">Magazin Papetarie</a>
        
        <div class="header-buttons">
            <a href="presentation.php" style="margin-right: 1rem;">Despre Proiect</a>
            <a href="app/controller/cart.php" style="margin-right: 1rem; font-weight:bold;">Cos <span id="cart-count"></span></a>
            <?php if (is_logged_in()): ?>
                <a href="app/controller/dashboard.php">Contul Meu</a>
                <?php if (is_admin()): ?>
                    <a href="app/controller/admin/products.php" style="margin-left: 1rem;">Admin</a>
                <?php endif; ?>
                <a href="app/controller/logout.php" style="margin-left: 1rem;">Deconectare</a>
            <?php else: ?>
                <a href="app/controller/login.php">Autentificare</a>
                <a href="app/controller/register.php" style="margin-left: 0.5rem;">Inregistrare</a>
            <?php endif; ?>
        </div>
    </header>

    <div style="max-width: 1200px; margin: 0 auto; padding: 2rem;">
        <h1 style="text-align: center; margin-bottom: 2rem;" class="text-red-500">Produse Recente</h1>
        
        <?php if (empty($products)): ?>
            <p style="text-align: center;">Nu exista produse momentan.</p>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($products as $p): ?>
                <div class="product-card">
                    <?php if($p['image_url']): ?>
                        <img src="<?= htmlspecialchars($p['image_url']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="product-image">
                    <?php else: ?>
                        <div class="product-image" style="display: flex; align-items: center; justify-content: center; color: #ccc;">No Image</div>
                    <?php endif; ?>
                    
                    <div class="product-details">
                        <div class="product-cat"><?= htmlspecialchars($p['subcategory_name'] ?? 'General') ?></div>
                        <h3 class="product-title"><?= htmlspecialchars($p['name']) ?></h3>
                        <div class="product-price"><?= number_format($p['price'], 2) ?> RON</div>
                        <a href="#" class="btn-add" data-id="<?= $p['id'] ?>">Adauga in Cos</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
