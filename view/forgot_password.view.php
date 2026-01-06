<!DOCTYPE html>
<html lang="en">
<head>
    <title>Resetează parola</title>
    <link rel="stylesheet" href="../../assets/login.css">
    <!-- Inline minimal style override if needed, reusing login.css -->
</head>
<body>
    <div class="page-container">
        <main class="form-card">
            <h2>Resetează parola</h2>
            <?php if (!empty($error)): ?>
                <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <p style="color: green;"><?php echo htmlspecialchars($success); ?></p>
            <?php endif; ?>

            <p style="margin-bottom: 20px; color: #666;">Introduceti adresa de email pentru a primi o noua parola temporara.</p>

            <form method="POST">
                <?php csrf_field(); ?>
                <div class="input-group">
                    <label for="email">Adresa de email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <br>
                <button type="submit" class="continue-button">Trimite parola nouă</button>
            </form>
            <div style="margin-top: 15px; font-size: 0.9em;">
                <p>Ți-ai amintit parola? <a href="login.php">Autentifică-te aici</a>.</p>
            </div>
        </main>
    </div>
</body>
</html>
