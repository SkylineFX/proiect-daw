<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register</title>
    <link rel="stylesheet" href="../../assets/login.css">
</head>
<body>
    <div class="page-container">
        <main class="form-card">
            <h2>Register</h2>
            <?php if ($error): ?>
                <p style="color: red;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php elseif (!empty($_SESSION['flash_success'])): ?>
                <p style="color: green;"><?php echo htmlspecialchars($_SESSION['flash_success'], ENT_QUOTES, 'UTF-8'); ?></p>
                <?php unset($_SESSION['flash_success']); ?>
            <?php endif; ?>

            <form method="POST">
                <?php csrf_field(); ?>
                <div class="input-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required maxlength="20" pattern="[A-Za-z0-9_]{2,20}">
                </div>
                <div class="input-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required maxlength="256">
                </div>
                <div class="input-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required minlength="8" maxlength="128">
                </div>
                <button type="submit" class="continue-button">Register</button>
            </form>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </main> 
    </div>
</body>
</html>