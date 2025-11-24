<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard</title>
</head>
<body>
    <h2>Welcome <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>

    <p>User Role: <strong><?php echo htmlspecialchars($_SESSION['role']); ?></strong></p>
    <p>Registration Date: <strong><?php echo htmlspecialchars($register_date); ?></strong></p>
    <small>Your user ID is: <?php echo htmlspecialchars($_SESSION['user_id']); ?></small>

    <p><a href="/">Home</a></p>
    <p><a href="logout.php">Logout</a></p>
</body>
</html>