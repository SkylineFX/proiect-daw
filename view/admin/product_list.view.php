<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Products</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="../../../assets/style.css">

</head>
<body>
    <header>
        <a href="../../../index.php">Home</a>
        <div class="header-buttons">
            <a href="../../logout.php">Logout</a>
        </div>
    </header>

    <div class="admin-container">
        <h1>Product Management</h1>
        
        <?php if (isset($_SESSION['flash_success'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?>
            </div>
        <?php endif; ?>

        <a href="products.php?action=create" class="btn btn-primary">Add New Product</a>
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
                        <a href="products.php?action=edit&id=<?= $p['id'] ?>" class="btn btn-primary">Edit</a>
                        <form action="products.php?action=delete&id=<?= $p['id'] ?>" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?')">
                            <?php csrf_field(); ?>
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
