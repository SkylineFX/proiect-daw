<?php
require_once __DIR__ . '/app/bootstrap.php';
require_once __DIR__ . '/app/model/Database.php';

try {
    $pdo = Database::getInstance()->getConnection();
    $sql = "ALTER TABLE orders ADD COLUMN delivery_address TEXT AFTER total_amount";
    $pdo->exec($sql);
    echo "Schema updated successfully: Added delivery_address column to orders table.\n";
} catch (PDOException $e) {
    echo "Error updating schema: " . $e->getMessage() . "\n";
}
