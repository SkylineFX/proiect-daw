<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Magazin Papetarie' ?></title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script>const CART_API_URL = '<?= URL_ROOT ?>/app/controller/cart_api.php';</script>
    <script src="<?= URL_ROOT ?>/assets/cart.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style type="text/tailwindcss">
        <?php require_once __DIR__ . '/tailwind_styles.php'; ?>
    </style>
</head>

<body class="bg-[#F9F5F2] text-[#2D3748] font-work-sans line-height-[1.6] antialiased">
    <header class="bg-[#F9F5F2] sticky top-0 z-50 px-4 py-4">
        <div class="max-w-[1200px] mx-auto w-full h-full flex justify-between items-center">
            <a href="<?= URL_ROOT ?>/index.php"><img src="<?= URL_ROOT ?>/assets/logo.svg" alt="Logo"></a>
            
            <div class="flex gap-4 items-center">
                <a href="<?= URL_ROOT ?>/app/controller/cart.php" class="hover:text-blue-600 transition-colors font-medium">Cos <span id="cart-count"></span></a>
                
                <?php if (function_exists('is_logged_in') && is_logged_in()): ?>
                    <div class="relative group">
                        <button class="flex items-center gap-1 hover:text-blue-600 transition-colors font-medium focus:outline-none">
                            Contul Meu
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <!-- Dropdown Menu -->
                        <div class="absolute right-0 mt-2 w-48 bg-white border-2 border-black rounded-sm opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <a href="<?= URL_ROOT ?>/app/controller/dashboard.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-t-sm">Profil</a>
                            <?php if (function_exists('is_admin') && is_admin()): ?>
                                <a href="<?= URL_ROOT ?>/app/controller/admin/products.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Admin</a>
                            <?php endif; ?>
                            <div class="border-t border-black"></div>
                            <a href="<?= URL_ROOT ?>/app/controller/logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-50 rounded-b-sm">Deconectare</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?= URL_ROOT ?>/app/controller/login.php" class="hover:text-blue-600 transition-colors font-medium">Autentificare</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
