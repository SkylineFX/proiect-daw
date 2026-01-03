<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../model/Product.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    redirect('index.php');
}

$productModel = new Product();
$product = $productModel->getProductById($id);

if (!$product) {
    // Optionally set a flash message
    redirect('index.php');
}

// Fetch category/subcategory name if not in product details (the getProductById might return raw row)
// The getAllProducts had joins. getProductById was simple select * from products.
// If we want category names, we might need a join or separate fetch. 
// For now, let's just show what we have. If we need category name, we can improve getProductById later.

require_once APP_ROOT . '/view/product.view.php';
