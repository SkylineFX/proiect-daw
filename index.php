<?php
require_once 'app/bootstrap.php';
require_once 'app/model/Product.php';

$productModel = new Product();

// Categories for Hero Navigation
$categories = $productModel->getCategoriesWithSubcategories();

// Pagination & Filtering Logic
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$limit = 12;
$offset = ($page - 1) * $limit;

$categoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;
$subcategoryId = isset($_GET['subcategory_id']) ? (int)$_GET['subcategory_id'] : null;

if ($subcategoryId) {
    // Subcategory Filtering has priority
    $products = $productModel->getProductsBySubcategoryPaginated($subcategoryId, $limit, $offset);
    $totalProducts = $productModel->getTotalProductCountBySubcategory($subcategoryId);
    
    // Find subcategory name for title (looping through categories to find it)
    $subcategoryName = 'Produse';
    // We also want to keep categoryId set if possible for the UI to maybe highlight parent category
    foreach ($categories as $cat) {
        foreach ($cat['subcategories'] as $sub) {
            if ($sub['id'] == $subcategoryId) {
                $subcategoryName = $sub['name'];
                $categoryId = $cat['id']; // Auto-set parent category
                break 2;
            }
        }
    }
    $pageTitle = $subcategoryName . ' - Magazin Online';
    $categoryName = $subcategoryName; 

} elseif ($categoryId) {
    // Category Filtering
    $products = $productModel->getProductsByCategoryPaginated($categoryId, $limit, $offset);
    $totalProducts = $productModel->getTotalProductCountByCategory($categoryId);
    // Find category name for title
    $categoryName = 'Produse';
    if(isset($categories[$categoryId])) {
        $categoryName = $categories[$categoryId]['name'];
    }
    $pageTitle = $categoryName . ' - Magazin Online';
} else {
    // All Products
    $products = $productModel->getProductsPaginated($limit, $offset);
    $totalProducts = $productModel->getTotalProductCount();
    $pageTitle = 'Magazin Online - Papetarie';
    $categoryName = 'Produse Recente';
}

$totalPages = ceil($totalProducts / $limit);

require_once 'view/partials/header.php';
?>

    <!-- Hero Component (Mega Menu) -->
    <div class="max-w-[1200px] mx-auto mb-8 bg-white p-1 rounded-md border-black border-2">
        <div class="hero-container" id="heroContainer">
            
            <!-- Category Navigation -->
            <div class="w-[250px] border-r border-gray-200 bg-gray-50 overflow-y-auto relative z-20 rounded-tl-sm rounded-bl-sm">
                <div class="text-bold p-4 bg-gray-900 text-white sticky top-0 z-10">
                    <a href="index.php" class="" data-has-subs="false">Produse</a>
                </div>
                <ul class="list-none p-0 m-0" id="categoryList">
                    <?php foreach ($categories as $cat): ?>
                    <li class="category-li">
                        <a href="index.php?category_id=<?= $cat['id'] ?>" class="block px-4 py-3 no-underline text-gray-800 bg-white border-b border-gray-200 transition-colors duration-200 relative cursor-pointer hover:bg-gray-100" data-has-subs="<?= !empty($cat['subcategories']) ? 'true' : 'false' ?>">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span><?= htmlspecialchars($cat['name']) ?></span>
                                <?php if (!empty($cat['subcategories'])): ?>
                                    <span style="font-size: 0.8em; color: #999;">&#9654;</span>
                                <?php endif; ?>
                            </div>
                        </a>
                        
                        <!-- Hidden Template for Subcategories -->
                        <?php if (!empty($cat['subcategories'])): ?>
                        <div class="subcat-source" style="display: none;">
                            <h3 style="margin-bottom: 1rem; border-bottom: 1px solid #eee; padding-bottom: 0.5rem;"><?= htmlspecialchars($cat['name']) ?></h3>
                            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                                <?php foreach ($cat['subcategories'] as $sub): ?>
                                    <a href="index.php?category_id=<?= $cat['id'] ?>&subcategory_id=<?= $sub['id'] ?>" class="decoration-none text-text-secondary p-2 bg-gray-100 rounded-sm">
                                        <?= htmlspecialchars($sub['name']) ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- The Overlay Panel -->
            <div id="mega-menu-overlay">
                <!-- Content injected via JS -->
            </div>

            <!-- Hero Carousel (Placeholder) -->
            <div class="hero-carousel">
                <div style="text-align: center; color: #999;">
                    <h2 style="margin-bottom: 1rem;">Bine ati venit!</h2>
                    <p>Descopera cele mai bune oferte la rechizite scolare.</p>
                    <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: center;">
                         <!-- Placeholder images -->
                         <div style="width: 150px; height: 100px; background: #ddd; display: flex; align-items: center; justify-content: center;">Slide 1</div>
                         <div style="width: 150px; height: 100px; background: #ddd; display: flex; align-items: center; justify-content: center;">Slide 2</div>
                         <div style="width: 150px; height: 100px; background: #ddd; display: flex; align-items: center; justify-content: center;">Slide 3</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const overlay = document.getElementById('mega-menu-overlay');
            const heroContainer = document.getElementById('heroContainer');
            const categoryItems = document.querySelectorAll('.category-li');

            categoryItems.forEach(item => {
                const link = item.querySelector('.category-item');
                const source = item.querySelector('.subcat-source');

                item.addEventListener('mouseenter', () => {
                    if (source) {
                        overlay.innerHTML = source.innerHTML;
                        overlay.style.display = 'block';
                        link.style.background = '#f0f0f0';
                    } else {
                        overlay.style.display = 'none';
                        link.style.background = '';
                    }
                });

                item.addEventListener('mouseleave', (e) => {
                    // Slight delay or check if moving to overlay could be added, 
                    // but for simplicity, we rely on the container mouseleave
                    // actually, if we leave the item, we might be entering the overlay OR another item.
                    // If we enter the overlay, we should keep it open.
                     link.style.background = '';
                });
            });

            // Logic to hide overlay when leaving the entire area (sidebar + overlay)
            // Or simpler: Hide when hovering a 'no-sub' item or leaving the container
            heroContainer.addEventListener('mouseleave', () => {
                overlay.style.display = 'none';
            });
            
            // Allow hovering the overlay itself to keep it open
            overlay.addEventListener('mouseenter', () => {
                overlay.style.display = 'block';
            });
        });
    </script>

    <div class="max-w-[1200px] mx-auto">
        <h1 class="text-2xl font-bold mt-12"><?= htmlspecialchars($categoryName) ?></h1>
        
        <?php if (empty($products)): ?>
            <p>Nu exista produse momentan in aceasta categorie.</p>
        <?php else: ?>
            <!-- Product Grid -->
            <div class="grid gap-8 mx-auto max-w-[1200px] grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <?php foreach ($products as $p): ?>
                <!-- Product Card -->
                <div class="bg-white overflow-hidden transition-all duration-300 flex flex-col border-black border-2 rounded-md hover:shadow-[8px_8px_0px_rgba(0,0,0,1)]">
                    <?php if($p['image_url']): ?>
                        <a href="app/controller/product.php?id=<?= $p['id'] ?>">
                            <img src="<?= htmlspecialchars($p['image_url']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="product-image">
                        </a>
                    <?php else: ?>
                        <a href="app/controller/product.php?id=<?= $p['id'] ?>" style="text-decoration:none">
                            <div class="product-image" style="display: flex; align-items: center; justify-content: center; color: #ccc;">No Image</div>
                        </a>
                    <?php endif; ?>
                    <!-- Product Details -->
                    <div class="p-6 flex-grow flex flex-col justify-between">
                        <!-- Product Category -->
                         <div class="flex flex-col gap-2">
                            <div class="text-xs uppercase tracking-wide text-text-secondary font-semibold mb-2"><?= htmlspecialchars($p['subcategory_name'] ?? 'General') ?></div>
                            <!-- Product Title -->
                            <h3 class="text-lg mb-auto text-text-primary">
                                <a href="app/controller/product.php?id=<?= $p['id'] ?>" style="color: inherit; text-decoration: none;">
                                    <?= htmlspecialchars($p['name']) ?>
                                </a>
                            </h3>
                         </div>
                         <div class="flex flex-col gap-2">
                            <div class="product-price"><?= number_format($p['price'], 2) ?> RON</div>
                            <a href="#" class="h-12 border-black border-2 p-2.5 bg-[#A6FAFF] hover:bg-[#79F7FF] hover:shadow-[2px_2px_0px_rgba(0,0,0,1)] active:bg-[#00E1EF] rounded-sm transition-all duration-200" data-id="<?= $p['id'] ?>">Adauga in Cos</a>
                         </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination Controls -->
            <div class="pagination" style="display: flex; justify-content: center; gap: 1rem; margin-top: 2rem; align-items: center;">
                <?php 
                    // Build base URL for pagination
                    $baseUrl = '?';
                    if ($categoryId) {
                        $baseUrl .= 'category_id=' . $categoryId . '&';
                    }
                    if ($subcategoryId) {
                        $baseUrl .= 'subcategory_id=' . $subcategoryId . '&';
                    }
                ?>
                
                <?php if ($page > 1): ?>
                    <a href="<?= $baseUrl ?>page=<?= $page - 1 ?>" class="btn-page" style="text-decoration: none; padding: 0.5rem 1rem; background: #eee; border-radius: 4px; color: #333;">&laquo; Previous</a>
                <?php endif; ?>

                <span>Page <?= $page ?> of <?= $totalPages ?></span>

                <?php if ($page < $totalPages): ?>
                    <a href="<?= $baseUrl ?>page=<?= $page + 1 ?>" class="btn-page" style="text-decoration: none; padding: 0.5rem 1rem; background: #eee; border-radius: 4px; color: #333;">Next &raquo;</a>
                <?php endif; ?>
            </div>

        <?php endif; ?>
    </div>

<?php require_once 'view/partials/footer.php'; ?>
