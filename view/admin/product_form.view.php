<?php
$pageTitle = ($product ? 'Edit' : 'Add') . ' Product';
require_once APP_ROOT . '/view/partials/header.php';
?>

    <div class="max-w-[1200px] mx-auto px-6 my-12">
        <h1><?= $product ? 'Editeaza Produs: ' . htmlspecialchars($product['name']) : 'Adauga Produs' ?></h1>

        <?php if (isset($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <form action="products.php?action=<?= $product ? 'update&id='.$product['id'] : 'store' ?>" method="POST" enctype="multipart/form-data">
            <?php csrf_field(); ?>

            <div class="form-group">
                <label>Nume Produs</label>
                <input type="text" name="name" class="form-control" value="<?= $product['name'] ?? '' ?>" required>
            </div>

            <div class="form-group">
                <label>Categorie</label>
                <select name="subcategory_id" class="form-control" required>
                    <option value="">Selecteaza Categorie</option>
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
                <label>Pret</label>
                <input type="number" name="price" step="0.01" class="form-control" value="<?= $product['price'] ?? '' ?>" required>
            </div>

            <div class="form-group">
                <label>Stoc</label>
                <input type="number" name="stock" class="form-control" value="<?= $product['stock'] ?? '0' ?>" required>
            </div>

            <div class="form-group">
                <label>Descriere</label>
                <textarea name="description" class="form-control" rows="5"><?= $product['description'] ?? '' ?></textarea>
            </div>

            <div class="form-group">
                <label>Imagine</label>
                <?php if ($product && $product['image_url']): ?>
                    <div style="margin-bottom:0.5rem"><img src="../../../<?= htmlspecialchars($product['image_url']) ?>" width="100"></div>
                <?php endif; ?>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>

            <button 
                class="h-12 border-black border-2 p-2.5 bg-[#A6FAFF] hover:bg-[#79F7FF] hover:shadow-[2px_2px_0px_rgba(0,0,0,1)] active:bg-[#00E1EF] rounded-sm transition-all duration-200" 
                type="submit">
                <?= $product ? 'Editeaza Produs' : 'Adauga Produs' ?>
            </button>
        </form>
    </div>
</body>
</html>
