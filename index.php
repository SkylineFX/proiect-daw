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
    <div class="max-w-[1200px] mx-auto my-8 bg-white p-1 rounded-md border-black border-2">
        <div class="hero-container" id="heroContainer">
            
            <!-- Category Navigation -->
            <div class="w-[250px] border-r border-gray-200 bg-gray-50 overflow-y-auto relative z-20 rounded-tl-sm rounded-bl-sm">
                <div class="text-bold p-4 bg-gray-900 text-white sticky top-0 z-10">
                    <a href="index.php" class="" data-has-subs="false">Produse</a>
                </div>
                <ul class="list-none p-0 m-0" id="categoryList">
                    <?php foreach ($categories as $cat): ?>
                    <li class="category-li">
                        <a href="index.php?category_id=<?= $cat['id'] ?>#products" class="block px-4 py-3 no-underline text-gray-800 bg-white border-b border-gray-200 transition-colors duration-200 relative cursor-pointer hover:bg-gray-100" data-has-subs="<?= !empty($cat['subcategories']) ? 'true' : 'false' ?>">
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
                                    <a href="index.php?category_id=<?= $cat['id'] ?>&subcategory_id=<?= $sub['id'] ?>#products" class="decoration-none text-text-secondary p-2 bg-gray-100 rounded-sm">
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

            <!-- Hero Carousel -->
            <div class="hero-carousel flex-grow relative overflow-hidden rounded-tr-md rounded-br-md">
                <!-- Slides -->
                <div class="carousel-slide absolute inset-0 transition-opacity duration-1000 opacity-100">
                    <img src="<?= URL_ROOT ?>/assets/hero-slide-1.jpg" alt="Hero Slide 1" class="w-full h-full object-cover">
                </div>
                <div class="carousel-slide absolute inset-0 transition-opacity duration-1000 opacity-0">
                    <img src="<?= URL_ROOT ?>/assets/hero-slide-2.jpg" alt="Hero Slide 2" class="w-full h-full object-cover">
                </div>

                <!-- Overlay Text -->
                <div class="absolute inset-0 bg-black/40 z-10 flex flex-col justify-center items-center text-white text-center p-8">
                    <h1 class="text-xl mb-6 drop-shadow-sm max-w-md" style="color: white;">Descopera cele mai bune oferte la rechizite si accesorii.</h1>
                    <a href="#products" class="px-8 py-3 bg-[#FFD43D] text-black font-bold text-lg rounded-sm hover:bg-[#F2C94C] transition-colors border-2 border-black shadow-[4px_4px_0px_rgba(0,0,0,1)] active:translate-x-[2px] active:translate-y-[2px] active:shadow-[2px_2px_0px_rgba(0,0,0,1)]">
                        Vezi Produse
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Carousel Logic
            const slides = document.querySelectorAll('.carousel-slide');
            let currentSlide = 0;
            
            if (slides.length > 0) {
                setInterval(() => {
                    slides[currentSlide].classList.remove('opacity-100');
                    slides[currentSlide].classList.add('opacity-0');
                    
                    currentSlide = (currentSlide + 1) % slides.length;
                    
                    slides[currentSlide].classList.remove('opacity-0');
                    slides[currentSlide].classList.add('opacity-100');
                }, 5000); // 5 seconds
            }

            const overlay = document.getElementById('mega-menu-overlay');
            const heroContainer = document.getElementById('heroContainer');
            const categoryItems = document.querySelectorAll('.category-li');

            categoryItems.forEach(item => {
                const link = item.querySelector('a'); // Target the main link
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
                    // If we enter the overlay, we should keep it open.
                     link.style.background = '';
                });
            });

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
        <h1 id="products" class="text-2xl font-bold mt-12"><?= htmlspecialchars($categoryName) ?></h1>
        
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
                            <a href="#" class="btn-add h-12 border-black border-2 p-2.5 bg-[#A6FAFF] hover:bg-[#79F7FF] hover:shadow-[2px_2px_0px_rgba(0,0,0,1)] active:bg-[#00E1EF] rounded-sm transition-all duration-200 block text-center flex items-center justify-center font-bold text-black no-underline" data-id="<?= $p['id'] ?>">Adauga in Cos</a>
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
