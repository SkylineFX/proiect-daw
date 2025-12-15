<?php
// app/controller/admin/products.php

require_once __DIR__ . '/../../bootstrap.php';
require_once APP_ROOT . '/app/model/Product.php';

require_admin(); // Ensure only admins can access

$productModel = new Product();
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

switch ($action) {
    case 'index':
        $products = $productModel->getAllProducts();
        require_once APP_ROOT . '/view/admin/product_list.view.php';
        break;

    case 'create':
        $categories = $productModel->getCategoriesWithSubcategories();
        $product = null; // Empty for create
        require_once APP_ROOT . '/view/admin/product_form.view.php';
        break;

    case 'store':
        verify_csrf();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle File Upload
            $image_url = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = APP_ROOT . '/assets/uploads/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                
                $fileName = time() . '_' . basename($_FILES['image']['name']);
                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fileName)) {
                    $image_url = 'assets/uploads/' . $fileName;
                }
            }

            $data = [
                'name' => sanitize($_POST['name']),
                'description' => sanitize($_POST['description']),
                'price' => (float) $_POST['price'],
                'stock' => (int) $_POST['stock'],
                'subcategory_id' => (int) $_POST['subcategory_id'],
                'image_url' => $image_url
            ];

            if ($productModel->createProduct($data)) {
                $_SESSION['flash_success'] = "Product created successfully!";
                redirect('app/controller/admin/products.php');
            } else {
                $error = "Failed to create product.";
                require_once APP_ROOT . '/view/admin/product_form.view.php';
            }
        }
        break;

    case 'edit':
        if (!$id) redirect('app/controller/admin/products.php');
        $product = $productModel->getProductById($id);
        $categories = $productModel->getCategoriesWithSubcategories();
        require_once APP_ROOT . '/view/admin/product_form.view.php';
        break;

    case 'update':
        verify_csrf();
        if (!$id) redirect('app/controller/admin/products.php');
        
        $currentProduct = $productModel->getProductById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
             // Handle File Upload
             $image_url = $currentProduct['image_url'];
             if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                 $uploadDir = APP_ROOT . '/assets/uploads/';
                 if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                 
                 $fileName = time() . '_' . basename($_FILES['image']['name']);
                 if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fileName)) {
                     $image_url = 'assets/uploads/' . $fileName;
                 }
             }
 
             $data = [
                 'name' => sanitize($_POST['name']),
                 'description' => sanitize($_POST['description']),
                 'price' => (float) $_POST['price'],
                 'stock' => (int) $_POST['stock'],
                 'subcategory_id' => (int) $_POST['subcategory_id'],
                 'image_url' => $image_url
             ];
 
             if ($productModel->updateProduct($id, $data)) {
                 $_SESSION['flash_success'] = "Product updated successfully!";
                 redirect('app/controller/admin/products.php');
             } else {
                 $error = "Failed to update product.";
                 require_once APP_ROOT . '/view/admin/product_form.view.php';
             }
        }
        break;

    case 'delete':
        verify_csrf();
        if ($id && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $productModel->deleteProduct($id);
            $_SESSION['flash_success'] = "Product deleted.";
        }
        redirect('app/controller/admin/products.php');
        break;

    default:
        redirect('app/controller/admin/products.php');
        break;
}
