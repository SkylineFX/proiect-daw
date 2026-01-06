<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../model/Database.php';

require_login();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $oldPassword = $_POST['old_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
        $error = "Please fill in all fields.";
    } elseif ($newPassword !== $confirmPassword) {
        $error = "New passwords do not match.";
    } elseif (strlen($newPassword) < 6) {
        $error = "New password must be at least 6 characters long.";
    } else {
        try {
            $pdo = Database::getInstance()->getConnection();
            
            // Verify Old Password
            $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $currentHash = $stmt->fetchColumn();

            if (!password_verify($oldPassword, $currentHash)) {
                $error = "Incorrect old password.";
            } else {
                // Update Password
                $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
                $updateStmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
                $updateStmt->execute([$newHash, $_SESSION['user_id']]);

                $success = "Password changed successfully!";
                // Optional: Force re-login or just stay logged in
            }

        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    }
}

require_once APP_ROOT . '/view/change_password.view.php';
