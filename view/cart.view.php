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
                                <button onclick="removeFromCart(<?= $item['id'] ?>)" class="bg-[#FF0C81] text-white text-xs font-bold border-black border-2 p-2 rounded-sm w-full">Sterge</button>
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
                <a href="../../index.php" class="h-12 border-black border-2 p-2.5 bg-gray-200 hover:bg-gray-300 hover:shadow-[2px_2px_0px_rgba(0,0,0,1)] active:bg-[#00E1EF] rounded-sm transition-all duration-200" style="margin-right: 1rem;">Continua Cumparaturile</a>
                <?php if (is_logged_in()): ?>
                    <a href="checkout.php" class="h-12 border-black border-2 p-2.5 bg-[#A6FAFF] hover:bg-[#79F7FF] hover:shadow-[2px_2px_0px_rgba(0,0,0,1)] active:bg-[#00E1EF] rounded-sm transition-all duration-200">Finalizeaza Comanda</a>
                <?php else: ?>
                    <a href="login.php" class="h-12 border-black border-2 p-2.5 bg-[#A6FAFF] hover:bg-[#79F7FF] hover:shadow-[2px_2px_0px_rgba(0,0,0,1)] active:bg-[#00E1EF] rounded-sm transition-all duration-200">Finalizeaza Comanda</a>
                <?php endif; ?>
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
