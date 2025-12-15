<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../../assets/login.css">
</head>
<body>
    <div class="page-container">
        <main class="form-card">
            <h2>Login</h2>
            <?php if ($error): ?>
                <p style="color: red;"><?php echo $error; ?></p>
            <?php endif; ?>

            <form method="POST">
                <?php csrf_field(); ?>
                <div class="input-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="input-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="continue-button">Login</button>
            </form>
            <p>Don't have an account? <a href="register.php">Register here</a>.</p>
        </main>
    </div>
</body>
</html>