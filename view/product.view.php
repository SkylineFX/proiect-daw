<?php
$pageTitle = htmlspecialchars($product['name']) . ' - Magazin Papetarie';
require_once APP_ROOT . '/view/partials/header.php';
?>

    <div class="product-page-container" style="max-width: 1200px; margin: 3rem auto; padding: 0 2rem;">
        
        <div style="margin-bottom: 1rem;">
            <a href="../../index.php" style="color: #718096; text-decoration: none;">&larr; Inapoi la produse</a>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; background: white; padding: 3rem; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            
            <!-- Product Image -->
            <div style="display: flex; align-items: center; justify-content: center; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 2rem;">
                <?php if($product['image_url']): ?>
                    <img src="../../<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="max-width: 100%; max-height: 500px; object-fit: contain;">
                <?php else: ?>
                    <div style="color: #ccc; font-size: 2rem;">No Image</div>
                <?php endif; ?>
            </div>

            <!-- Product Details -->
            <div>
                <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem; color: #1a202c;"><?= htmlspecialchars($product['name']) ?></h1>
                
                <div style="font-size: 1.5rem; font-weight: 700; color: #3b82f6; margin-bottom: 2rem;">
                    <?= number_format($product['price'], 2) ?> RON
                </div>

                <div style="margin-bottom: 2rem; color: #4a5568; line-height: 1.8;">
                    <h3 style="color: #2d3748; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.5rem; margin-bottom: 1rem;">Descriere</h3>
                    <?= nl2br(htmlspecialchars($product['description'])) ?>
                </div>

                <div style="background: #f8fafc; padding: 1.5rem; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <div style="margin-bottom: 1rem;">
                        <strong>Stoc:</strong> 
                        <?php if($product['stock'] > 10): ?>
                            <span style="color: #10b981;">In Stoc (<?= $product['stock'] ?> buc)</span>
                        <?php elseif($product['stock'] > 0): ?>
                            <span style="color: #f59e0b;">Stoc Limitat (<?= $product['stock'] ?> buc)</span>
                        <?php else: ?>
                            <span style="color: #ef4444;">Stoc Epuizat</span>
                        <?php endif; ?>
                    </div>

                    <?php if($product['stock'] > 0): ?>
                        <button class="btn-add" data-id="<?= $product['id'] ?>" style="width: 100%; padding: 1rem; font-size: 1.1rem; justify-content: center; display: flex;">
                            Adauga in Cos
                        </button>
                    <?php else: ?>
                        <button class="btn" disabled style="width: 100%; padding: 1rem; background: #cbd5e1; cursor: not-allowed; color: #64748b;">
                            Indisponibil Momentan
                        </button>
                    <?php endif; ?>
                </div>
            </div>

        </div>

    </div>

</body>
</html>
