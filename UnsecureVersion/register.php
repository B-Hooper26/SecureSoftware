<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $fullname = $_POST['fullname'] ?? '';
    $email = $_POST['email'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $password = $_POST['password'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    // Vulnerable SQL (no prepared statements, no escaping, no duplicate checks)
    $sql = "INSERT INTO users (username, fullname, email, dob, password, phone, address, is_admin) VALUES ('$username', '$fullname', '$email', '$dob', '$password', '$phone', '$address', $is_admin)";
    if ($conn->query($sql)) {
        header('Location: login.php?registered=1');
        exit;
    } else {
        $errors[] = 'Registration failed. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="/UnsecureVersion/style.css">
    <link rel="stylesheet" href="/UnsecureVersion/indexpage.css">
</head>
<body>
<div class="logo-row">
    <img src="images/logo.png" alt="Company Logo" class="logo-img">
    <span class="company-name">InnovativeTech Solutions.</span>
</div>
<div class="center-content">
    <form action="register.php" method="post" class="form-box" id="register-form">
        <h2>Register</h2>
        <?php if (!empty($errors)): ?>
            <div style="color:red; margin-bottom:10px;">
                <?php foreach ($errors as $error) echo htmlspecialchars($error) . '<br>'; ?>
            </div>
        <?php endif; ?>
        <input type="text" name="username" placeholder="Username" required>
        <input type="text" name="fullname" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="date" name="dob" placeholder="Date of Birth" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="tel" name="phone" placeholder="Phone Number" required>
        <textarea name="address" placeholder="Address" required rows="3"></textarea>
        <div class="admin-check">
            <label>
                <input type="checkbox" name="is_admin"> Register as Admin
            </label>
        </div>
        <button type="submit" name="register">Register</button>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </form>
</div>
</body>
</html> 