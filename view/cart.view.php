<?php
$pageTitle = 'Cos Cumparaturi';
require_once APP_ROOT . '/view/partials/header.php';
?>

    <div class="common-container max-w-[1200px] mx-auto my-12">
        <h1>Cosul de Cumparaturi</h1>

        <?php if (empty($cartItems)): ?>
            <div class="alert">
                Cosul tau este gol. <a href="../../index.php">Vezi produse</a>.
            </div>
        <?php else: ?>
            <div class="bg-white border-2 border-black rounded-sm overflow-hidden">
                <table class="table" style="margin-top: 0; box-shadow: none;">
                    <thead>
                        <tr>
                            <th>Produs</th>
                            <th>Pret</th>
                            <th>Cantitate</th>
                            <th>Total</th>
                            <th>Actiuni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cartItems as $item): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <?php if($item['image_url']): ?>
                                        <img src="../../<?= htmlspecialchars($item['image_url']) ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                    <?php endif; ?>
                                    <b><?= htmlspecialchars($item['name']) ?></b>
                                </div>
                            </td>
                            <td><?= number_format($item['price'], 2) ?> RON</td>
                            <td>
                                <!-- Simple quantity display for now, could be input -->
                                <?= $item['qty'] ?>
                            </td>
                            <td><?= number_format($item['line_total'], 2) ?> RON</td>
                            <td>
                                <button onclick="removeFromCart(<?= $item['id'] ?>)" class="btn-danger" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">Sterge</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <tr style="font-weight: bold; background: #f8fafc;">
                            <td colspan="3" style="text-align: right;">Total General:</td>
                            <td colspan="2"><?= number_format($totalPrice, 2) ?> RON</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 2rem; text-align: right;">
                <a href="../../index.php" class="btn" style="background: #cbd5e1; margin-right: 1rem;">Continua Cumparaturile</a>
                <a href="#" onclick="alert('Checkout not implemented yet!')" class="btn-primary">Finalizeaza Comanda</a>
            </div>
        <?php endif; ?>
    </div>

    <script>
    // Inline script for removed logic or extend assets/cart.js
    async function removeFromCart(id) {
        if(!confirm('Stergi acest produs?')) return;
        
        try {
            const response = await fetch('cart_api.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ action: 'remove', product_id: id })
            });
            const data = await response.json();
            if(data.success) {
                location.reload();
            }
        } catch(e) { console.error(e); }
    }
    </script>
</body>
</html>
