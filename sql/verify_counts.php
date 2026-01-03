<?php
// sql/verify_counts.php
require_once __DIR__ . '/../app/bootstrap.php';
$db = Database::getInstance();
$pdo = $db->getConnection();

$stmt = $pdo->query("SELECT COUNT(*) FROM categories");
echo "Categories count: " . $stmt->fetchColumn() . PHP_EOL;

$stmt = $pdo->query("SELECT COUNT(*) FROM subcategories");
echo "Subcategories count: " . $stmt->fetchColumn() . PHP_EOL;
