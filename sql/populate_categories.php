<?php
// sql/populate_categories.php

// Adjust path to bootstrap as we are in sql/ folder
require_once __DIR__ . '/../app/bootstrap.php';

// Get Database Connection
try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
} catch (Exception $e) {
    die("Could not connect to database: " . $e->getMessage() . PHP_EOL);
}

// Read JSON file
$jsonFile = __DIR__ . '/categories.json';
if (!file_exists($jsonFile)) {
    die("Error: categories.json not found in " . __DIR__ . PHP_EOL);
}

$jsonData = file_get_contents($jsonFile);
$categoriesData = json_decode($jsonData, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error decoding JSON: " . json_last_error_msg() . PHP_EOL);
}

echo "Starting import..." . PHP_EOL;

// Prepare statements
$stmtInsertCategory = $pdo->prepare("INSERT INTO categories (name, description) VALUES (:name, :description)");
$stmtInsertSubcategory = $pdo->prepare("INSERT INTO subcategories (category_id, name, description) VALUES (:category_id, :name, :description)");

$pdo->beginTransaction();

try {
    foreach ($categoriesData as $catData) {
        $categoryName = $catData['category'];
        
        // Check if category already exists to avoid duplicates (optional but good practice)
        // For now, assuming fresh or appending is fine. If unique constraint exists, it will fail.
        // But schema doesn't show unique constraint on name.
        
        // Insert Category
        echo "Inserting Category: $categoryName" . PHP_EOL;
        $stmtInsertCategory->execute([
            ':name' => $categoryName,
            ':description' => '' // No description in JSON
        ]);
        
        $categoryId = $pdo->lastInsertId();
        
        // Insert Subcategories
        if (isset($catData['subcategories']) && is_array($catData['subcategories'])) {
            foreach ($catData['subcategories'] as $subData) {
                $subcategoryName = $subData['subcategory_name'];
                
                echo "  - Inserting Subcategory: $subcategoryName" . PHP_EOL;
                $stmtInsertSubcategory->execute([
                    ':category_id' => $categoryId,
                    ':name' => $subcategoryName,
                    ':description' => '' // No description in JSON
                ]);
            }
        }
    }
    
    $pdo->commit();
    echo "Import completed successfully!" . PHP_EOL;
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error during import: " . $e->getMessage() . PHP_EOL;
}
