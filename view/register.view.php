<!DOCTYPE html>
<html lang="en">
<head>
    <title>Înregistrare</title>
    <link rel="stylesheet" href="../../assets/login.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <div class="page-container">
        <main class="form-card">
            <h2>Înregistrare</h2>
            <?php if ($error): ?>
                <p style="color: red;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php elseif (!empty($_SESSION['flash_success'])): ?>
                <p style="color: green;"><?php echo htmlspecialchars($_SESSION['flash_success'], ENT_QUOTES, 'UTF-8'); ?></p>
                <?php unset($_SESSION['flash_success']); ?>
            <?php endif; ?>

            <form method="POST">
                <?php csrf_field(); ?>
                <div class="input-group">
                    <label for="username">Nume:</label>
                    <input type="text" id="username" name="username" required maxlength="20" pattern="[A-Za-z0-9_]{2,20}">
                </div>
                <div class="input-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required maxlength="256">
                </div>
                <div class="input-group">
                    <label for="password">Parola:</label>
                    <input type="password" id="password" name="password" required minlength="8" maxlength="128">
                </div>
                <div class="g-recaptcha" data-sitekey="<?php echo RECAPTCHA_SITE_KEY; ?>"></div>
                <br>
                <button type="submit" class="continue-button">Înregistrează-te</button>
            </form>
            <p>Ai deja cont? <a href="login.php">Autentifică-te aici</a>.</p>
        </main> 
    </div>
</body>
</html>