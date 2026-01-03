<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Magazin Papetarie' ?></title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script>const CART_API_URL = '<?= URL_ROOT ?>/app/controller/cart_api.php';</script>
    <script src="<?= URL_ROOT ?>/assets/cart.js"></script>
    <link rel="stylesheet" href="<?= URL_ROOT ?>/assets/style.css">
</head>
<body>
    <header>
        <a href="<?= URL_ROOT ?>/index.php">Magazin Papetarie</a>
        
        <div class="header-buttons">
            <a href="<?= URL_ROOT ?>/index.php">Acasa</a>
            <a href="<?= URL_ROOT ?>/presentation.php" style="margin-right: 1rem;">Despre Proiect</a>
            <a href="<?= URL_ROOT ?>/app/controller/cart.php" style="margin-right: 1rem; font-weight:bold;">Cos <span id="cart-count"></span></a>
            <?php if (function_exists('is_logged_in') && is_logged_in()): ?>
                <a href="<?= URL_ROOT ?>/app/controller/dashboard.php">Contul Meu</a>
                <?php if (function_exists('is_admin') && is_admin()): ?>
                    <a href="<?= URL_ROOT ?>/app/controller/admin/products.php">Admin</a>
                <?php endif; ?>
                <a href="<?= URL_ROOT ?>/app/controller/logout.php" style="margin-left: 1rem;">Deconectare</a>
            <?php else: ?>
                <a href="<?= URL_ROOT ?>/app/controller/login.php">Autentificare</a>
            <?php endif; ?>
        </div>
    </header>
