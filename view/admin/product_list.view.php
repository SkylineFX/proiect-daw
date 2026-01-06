<?php
$pageTitle = 'Admin - Products';
require_once APP_ROOT . '/view/partials/header.php';
?>

    <div class="max-w-[1200px] mx-auto px-6 my-12">
        <div class="flex justify-between items-center">
            <h1>Product Management</h1>
            <a 
                href="products.php?action=create" 
                class="h-12 border-black border-2 p-2.5 bg-[#A6FAFF] hover:bg-[#79F7FF] hover:shadow-[2px_2px_0px_rgba(0,0,0,1)] active:bg-[#00E1EF] rounded-sm transition-all duration-200"
            >
                Add New Product
            </a>
        </div>

        <?php if (isset($_SESSION['flash_success'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?>
            </div>
        <?php endif; ?>

        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td>
                        <?php if($p['image_url']): ?>
                            <img src="../../../<?= htmlspecialchars($p['image_url']) ?>" alt="img" width="50">
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td><?= htmlspecialchars($p['subcategory_name'] ?? 'N/A') ?></td>
                    <td>$<?= number_format($p['price'], 2) ?></td>
                    <td><?= $p['stock'] ?></td>
                    <td>
                        <div class="flex flex-col items-start justify-center gap-1">
                            <a 
                                href="products.php?action=edit&id=<?= $p['id'] ?>" 
                                class="bg-[#FFD43D] text-black text-xs font-bold border-black border-2 p-1 rounded-sm"
                            >
                                Edit
                            </a>
                            <form action="products.php?action=delete&id=<?= $p['id'] ?>" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?')">
                                <?php csrf_field(); ?>
                                <button 
                                    type="submit" 
                                    class="bg-[#FF0C81] text-white text-xs font-bold border-black border-2 p-1 rounded-sm"
                                >
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
