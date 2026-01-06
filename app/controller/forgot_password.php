<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../model/MailService.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $email = sanitize($_POST['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        try {
            $pdo = Database::getInstance()->getConnection();
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                // Generate secure random password
                $newPassword = bin2hex(random_bytes(4)); // 8 chars
                $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);

                // Update DB
                $updateStmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
                $updateStmt->execute([$passwordHash, $user['id']]);

                // Send Email
                if (MailService::sendPasswordReset($email, $user['username'], $newPassword)) {
                    $success = "Dacă acest email este înregistrat, a fost trimisă o nouă parolă."; 
                } else {
                    $error = "A esuat trimiterea email-ului. Vă rugăm încercați din nou.";
                }
            } else {
                $error = "Nu s-a gasit un cont cu acest email."; 
            }

        } catch (Exception $e) {
            $error = "A apărut o eroare. Vă rugăm încercați din nou.";
            error_log($e->getMessage());
        }
    }
}

require_once APP_ROOT . '/view/forgot_password.view.php';
