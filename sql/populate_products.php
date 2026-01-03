<?php
// sql/populate_products.php

require_once __DIR__ . '/../app/bootstrap.php';

// Configuration
$jsonFile = __DIR__ . '/products.json';
$imagesDir = __DIR__ . '/../assets/products';
$sleepMicroseconds = 200000; // 0.2 seconds delay between requests (5 requests/sec)

// Create images directory
if (!is_dir($imagesDir)) {
    if (!mkdir($imagesDir, 0755, true)) {
        die("Failed to create images directory: $imagesDir" . PHP_EOL);
    }
}

// Get Database Connection
try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
} catch (Exception $e) {
    die("Could not connect to database: " . $e->getMessage() . PHP_EOL);
}

// Read JSON file
if (!file_exists($jsonFile)) {
    die("Error: products.json not found." . PHP_EOL);
}

echo "Reading JSON file..." . PHP_EOL;
$jsonData = file_get_contents($jsonFile);
$productsData = json_decode($jsonData, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error decoding JSON: " . json_last_error_msg() . PHP_EOL);
}

$totalProducts = count($productsData);
echo "Found $totalProducts products to import." . PHP_EOL;

// 1. Update Schema if needed (Add sku and scraped_id columns)
// We'll check if columns exist first to avoid errors on re-run
echo "Checking schema..." . PHP_EOL;
try {
    $pdo->exec("ALTER TABLE products ADD COLUMN sku VARCHAR(100) AFTER subcategory_id");
    echo "Added 'sku' column." . PHP_EOL;
} catch (PDOException $e) {
    // Column likely exists
}
try {
    $pdo->exec("ALTER TABLE products ADD COLUMN scraped_id INT AFTER id");
    echo "Added 'scraped_id' column." . PHP_EOL;
} catch (PDOException $e) {
    // Column likely exists
}


// 2. Cache subcategories for faster lookup
// Map: "Category Name|Subcategory Name" => subcategory_id
$subMap = [];
$stmt = $pdo->query("
    SELECT s.id, s.name as sub_name, c.name as cat_name 
    FROM subcategories s 
    JOIN categories c ON s.category_id = c.id
");
while ($row = $stmt->fetch()) {
    $key = strtolower(trim($row['cat_name'])) . '|' . strtolower(trim($row['sub_name']));
    $subMap[$key] = $row['id'];
}

// Prepare Insert Statement
$stmtInsert = $pdo->prepare("
    INSERT INTO products (subcategory_id, scraped_id, sku, name, description, price, stock, image_url) 
    VALUES (:subcategory_id, :scraped_id, :sku, :name, :description, :price, :stock, :image_url)
");

echo "Starting import..." . PHP_EOL;

$count = 0;
$skipped = 0;

// To avoid memory issues with large imports, we might transactionalize in chunks, 
// but for simplicity we'll commit every 50 items or just one big transaction if size permits.
// Given 5MB json, one transaction might be okay, but images make it slow, so maybe commit often.

foreach ($productsData as $index => $item) {
    $count++;
    
    // Progress
    if ($count % 10 == 0) {
        echo "Processing $count / $totalProducts..." . PHP_EOL;
    }

    $catName = $item['category'];
    $subName = $item['subcategory'];
    $key = strtolower(trim($catName)) . '|' . strtolower(trim($subName));

    if (!isset($subMap[$key])) {
        echo "  [Warning] Subcategory not found: '$catName' -> '$subName'. Skipping product: {$item['name']}" . PHP_EOL;
        $skipped++;
        continue;
    }

    $subId = $subMap[$key];
    $scrapedId = $item['product_id'];
    $sku = $item['sku'];
    $name = $item['name'];
    $price = $item['price'];
    $remoteImageUrl = $item['image_url'];
    
    // Image Handling
    $localImageName = "product_{$scrapedId}.jpg";
    $localImagePath = $imagesDir . '/' . $localImageName;
    $dbImageUrl = 'assets/products/' . $localImageName;

    // Download image if not exists
    if (!file_exists($localImagePath)) {
        if (!empty($remoteImageUrl)) {
            $imageData = @file_get_contents($remoteImageUrl);
            if ($imageData !== false) {
                file_put_contents($localImagePath, $imageData);
                usleep($sleepMicroseconds); // Rate limit
            } else {
                echo "  [Warning] Failed to download image for ID $scrapedId" . PHP_EOL;
                $dbImageUrl = null; // Or keep default
            }
        } else {
            $dbImageUrl = null;
        }
    }

    // Insert
    try {
        $stmtInsert->execute([
            ':subcategory_id' => $subId,
            ':scraped_id' => $scrapedId,
            ':sku' => $sku,
            ':name' => $name,
            ':description' => '', // No description in JSON
            ':price' => $price,
            ':stock' => 1, // Force stock to 1
            ':image_url' => $dbImageUrl
        ]);
    } catch (PDOException $e) {
        echo "  [Error] DB Insert failed for ID $scrapedId: " . $e->getMessage() . PHP_EOL;
    }
}

echo "Import finished. Processed: $count. Skipped: $skipped." . PHP_EOL;
