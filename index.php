<?php
require_once 'app/bootstrap.php';
require_once 'app/model/Product.php';

$productModel = new Product();
$products = $productModel->getAllProducts();


$pageTitle = 'Magazin Online - Papetarie';
require_once 'view/partials/header.php';
?>

    <div style="max-width: 1200px; margin: 0 auto; padding: 2rem;">
        <h1 style="text-align: center; margin-bottom: 2rem;" class="text-red-500">Produse Recente</h1>
        
        <?php if (empty($products)): ?>
            <p style="text-align: center;">Nu exista produse momentan.</p>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($products as $p): ?>
                <div class="product-card">
                    <?php if($p['image_url']): ?>
                        <a href="app/controller/product.php?id=<?= $p['id'] ?>">
                            <img src="<?= htmlspecialchars($p['image_url']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="product-image">
                        </a>
                    <?php else: ?>
                        <a href="app/controller/product.php?id=<?= $p['id'] ?>" style="text-decoration:none">
                            <div class="product-image" style="display: flex; align-items: center; justify-content: center; color: #ccc;">No Image</div>
                        </a>
                    <?php endif; ?>
                    
                    <div class="product-details">
                        <div class="product-cat"><?= htmlspecialchars($p['subcategory_name'] ?? 'General') ?></div>
                        <h3 class="product-title">
                            <a href="app/controller/product.php?id=<?= $p['id'] ?>" style="color: inherit; text-decoration: none;">
                                <?= htmlspecialchars($p['name']) ?>
                            </a>
                        </h3>
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
