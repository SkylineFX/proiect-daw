<!DOCTYPE html>
<html lang="en">
<head>
    <title>Autentificare</title>
    <link rel="stylesheet" href="../../assets/login.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <div class="page-container">
        <main class="form-card">
            <h2>Autentificare</h2>
            <?php if ($error): ?>
                <p style="color: red;"><?php echo $error; ?></p>
            <?php endif; ?>

            <form method="POST">
                <?php csrf_field(); ?>
                <div class="input-group">
                    <label for="username">Nume:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="input-group">
                    <label for="password">Parola:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="g-recaptcha" data-sitekey="<?php echo RECAPTCHA_SITE_KEY; ?>"></div>
                <br>
                <button type="submit" class="continue-button">Autentifică-te</button>
            </form>
            <div style="margin-top: 15px; font-size: 0.9em;">
                <p>Nu ai cont? <a href="register.php">Înregistrează-te aici</a>.</p>
                <p>Ți-ai uitat parola? <a href="forgot_password.php">Resetează-o aici</a>.</p>
            </div>
        </main>
    </div>
</body>
</html>