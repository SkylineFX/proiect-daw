<?php

require '../model/Database.php';

// Redirect unauthenticated users to the login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

try {
    $pdo = Database::getInstance()->getConnection();
} catch (PDOException $e) {
    error_log('Database connection failed: ', $e->getMessage());
    echo "Internal error. Please try again later.";
    exit();
}

$register_date = 'N/A'; 
try {
    $stmt = $pdo->prepare('SELECT register_date FROM users WHERE id = ?');
    
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch();

    if ($result) {
        $register_date = $result['register_date']; 
    }

} catch (PDOException $e) {
    error_log('Dashboard DB Error: ' . $e->getMessage());
}


require_once '../../view/dashboard.view.php'
?>