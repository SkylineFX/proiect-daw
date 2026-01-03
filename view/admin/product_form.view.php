<?php
$pageTitle = ($product ? 'Edit' : 'Add') . ' Product';
require_once APP_ROOT . '/view/partials/header.php';
?>

    <div class="admin-container">
        <h1><?= $product ? 'Edit Product: ' . htmlspecialchars($product['name']) : 'Add New Product' ?></h1>

        <?php if (isset($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <form action="products.php?action=<?= $product ? 'update&id='.$product['id'] : 'store' ?>" method="POST" enctype="multipart/form-data">
            <?php csrf_field(); ?>

            <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="name" class="form-control" value="<?= $product['name'] ?? '' ?>" required>
            </div>

            <div class="form-group">
                <label>Category</label>
                <select name="subcategory_id" class="form-control" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <optgroup label="<?= htmlspecialchars($cat['name']) ?>">
                            <?php foreach ($cat['subcategories'] as $sub): ?>
                                <option value="<?= $sub['id'] ?>" <?= ($product && $product['subcategory_id'] == $sub['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($sub['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Price</label>
                <input type="number" name="price" step="0.01" class="form-control" value="<?= $product['price'] ?? '' ?>" required>
            </div>

            <div class="form-group">
                <label>Stock</label>
                <input type="number" name="stock" class="form-control" value="<?= $product['stock'] ?? '0' ?>" required>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="5"><?= $product['description'] ?? '' ?></textarea>
            </div>

            <div class="form-group">
                <label>Image</label>
                <?php if ($product && $product['image_url']): ?>
                    <div style="margin-bottom:0.5rem"><img src="../../../<?= htmlspecialchars($product['image_url']) ?>" width="100"></div>
                <?php endif; ?>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>

            <button type="submit" class="btn btn-primary"><?= $product ? 'Update Product' : 'Create Product' ?></button>
        </form>
    </div>
</body>
</html>
