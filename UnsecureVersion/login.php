<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
<<<<<<< HEAD
require_once 'unsecure_config.php';
$config = require 'unsecure_config.php';
=======
require_once 'config.php';
$config = require 'config.php';
>>>>>>> 7ae2812b34732bf97338b97e0470cfaba8afc6f2
$conn = new mysqli(
    $config['DB_SERVER'],
    $config['DB_USERNAME'],
    $config['DB_PASSWORD'],
    $config['DB_NAME']
);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_or_email = $_POST['username_or_email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Even more vulnerable SQL (no parentheses, no escaping)
    $sql = "SELECT id, username, password, is_admin FROM users WHERE username = '$username_or_email' OR email = '$username_or_email' OR password = '$password'";
    echo "<pre>$sql</pre>";
    $result = $conn->query($sql);
    if ($result && $result->num_rows >= 1) {
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['is_admin'] = $row['is_admin'];
        header('Location: welcome.php');
        exit;
    } else {
        $error = 'No account found with that username/email and password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="/UnsecureVersion/style.css">
    <link rel="stylesheet" href="/UnsecureVersion/indexpage.css">
</head>
<body>
<div class="logo-row">
    <img src="images/logo.png" alt="Company Logo" class="logo-img">
    <span class="company-name">InnovativeTech Solutions.</span>
</div>
<div class="center-content">
    <form action="login.php" method="post" class="form-box" id="login-form">
        <h2>Login</h2>
        <?php if ($error): ?>
            <div style="color:red; margin-bottom:10px;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        <input type="text" name="username_or_email" placeholder="Username or Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </form>
</div>
</body>
</html> 