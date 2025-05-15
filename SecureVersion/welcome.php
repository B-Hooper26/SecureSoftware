<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
$is_admin = $_SESSION['is_admin'] ?? 0;
$username = htmlspecialchars($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <div class="form-box" style="text-align:center;">
        <h2>
            <?php if ($is_admin): ?>
                Hello Admin
            <?php else: ?>
                Hello <?php echo $username; ?>
            <?php endif; ?>
        </h2>
        <p>Welcome to the secure area.</p>
        <a href="logout.php" style="display:inline-block;margin-top:20px;">Logout</a>
    </div>
</div>
</body>
</html> 