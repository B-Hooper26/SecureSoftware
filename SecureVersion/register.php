<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

<<<<<<< HEAD
require_once 'secure_config.php';
=======
require_once 'config.php';
>>>>>>> 7ae2812b34732bf97338b97e0470cfaba8afc6f2

function is_strong_password($password) {
    return strlen($password) >= 8 &&
        preg_match('/[A-Z]/', $password) &&
        preg_match('/[a-z]/', $password) &&
        preg_match('/[0-9]/', $password) &&
        preg_match('/[^a-zA-Z0-9]/', $password);
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $password = $_POST['password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    // Basic validation
    if (!$username || !$fullname || !$email || !$dob || !$password || !$phone || !$address) {
        $errors[] = 'All fields are required.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email address.';
    }
    if (!is_strong_password($password)) {
        $errors[] = 'Password must be at least 8 characters, include an uppercase letter, a lowercase letter, a digit, and a special character.';
    }
    if (empty($errors)) {
        // Check for existing user/email
        $stmt = $conn->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
        $stmt->bind_param('ss', $username, $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = 'Username or email already exists.';
        }
        $stmt->close();
    }
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare('INSERT INTO users (username, fullname, email, dob, password, phone, address, is_admin) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('sssssssi', $username, $fullname, $email, $dob, $hashed_password, $phone, $address, $is_admin);
        if ($stmt->execute()) {
            header('Location: login.php?registered=1');
            exit;
        } else {
            $errors[] = 'Registration failed. Please try again.';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <div class="form-box" id="register-form">
        <form action="register.php" method="post">
            <h2>Register</h2>
            <?php if (!empty($errors)): ?>
                <div style="color:red; margin-bottom:10px;">
                    <?php foreach ($errors as $error) echo htmlspecialchars($error) . '<br>'; ?>
                </div>
            <?php endif; ?>
            <input type="text" name="username" placeholder="Username" required minlength="3" maxlength="20" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
            <input type="text" name="fullname" placeholder="Full Name" required value="<?php echo htmlspecialchars($_POST['fullname'] ?? ''); ?>">
            <input type="email" name="email" placeholder="Email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            <input type="date" name="dob" placeholder="Date of Birth" required value="<?php echo htmlspecialchars($_POST['dob'] ?? ''); ?>">
            <input type="password" name="password" placeholder="Password" required minlength="8">
            <input type="tel" name="phone" placeholder="Phone Number" required pattern="[0-9]{10,15}" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
            <textarea name="address" placeholder="Address" required rows="3"><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
            <div class="admin-check">
                <label>
                    <input type="checkbox" name="is_admin" <?php if (!empty($_POST['is_admin'])) echo 'checked'; ?>> Register as Admin
                </label>
            </div>
            <button type="submit" name="register">Register</button>
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </form>
    </div>
</div>
</body>
</html> 