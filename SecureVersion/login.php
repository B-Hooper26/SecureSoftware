<?php
session_start();
require_once 'config.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_or_email = trim($_POST['username_or_email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$username_or_email || !$password) {
        $error = 'Please enter your username/email and password.';
    } else {
        $stmt = $conn->prepare('SELECT id, username, password, is_admin FROM users WHERE username = ? OR email = ?');
        $stmt->bind_param('ss', $username_or_email, $username_or_email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $username, $hashed_password, $is_admin);
            $stmt->fetch();
            
            // Debug information
            error_log("Attempting login for user: " . $username_or_email);
            error_log("Stored hash: " . $hashed_password);
            error_log("Password verification result: " . (password_verify($password, $hashed_password) ? 'true' : 'false'));
            
            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $username;
                $_SESSION['is_admin'] = $is_admin;
                header('Location: welcome.php');
                exit;
            } else {
                $error = 'Invalid password.';
            }
        } else {
            $error = 'No account found with that username or email.';
        }
        $stmt->close();
    }
}
?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <div class="form-box" id="login-form">
        <form action="login.php" method="post">
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
</div>
</body>
</html> 