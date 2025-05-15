<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];

require_once 'config.php';

// Fetch current password hash
$stmt = $conn->prepare('SELECT password FROM users WHERE id = ?');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($db_password_hash);
$stmt->fetch();
$stmt->close();

$pw_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $old_pw = $_POST['old_password'] ?? '';
    $new_pw = $_POST['new_password'] ?? '';
    $confirm_pw = $_POST['confirm_password'] ?? '';
    if (!password_verify($old_pw, $db_password_hash)) {
        $pw_msg = 'Old password is incorrect.';
    } elseif ($new_pw !== $confirm_pw) {
        $pw_msg = 'New passwords do not match.';
    } elseif (!is_strong_password($new_pw)) {
        $pw_msg = 'Password must be at least 8 characters, include an uppercase letter, a lowercase letter, a digit, and a special character.';
    } else {
        $new_hash = password_hash($new_pw, PASSWORD_DEFAULT);
        $stmt = $conn->prepare('UPDATE users SET password=? WHERE id=?');
        $stmt->bind_param('si', $new_hash, $user_id);
        if ($stmt->execute()) {
            header('Location: welcome.php?pwmsg=success');
            exit;
        } else {
            $pw_msg = 'Failed to change password. Please try again.';
        }
        $stmt->close();
    }
}

function is_strong_password($password) {
    return strlen($password) >= 8 &&
        preg_match('/[A-Z]/', $password) &&
        preg_match('/[a-z]/', $password) &&
        preg_match('/[0-9]/', $password) &&
        preg_match('/[^a-zA-Z0-9]/', $password);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    <link rel="stylesheet" href="/SecureVersion/style.css">
    <link rel="stylesheet" href="/SecureVersion/indexpage.css">
    <style>
        .form-box {
            max-width: 400px;
            margin: 60px auto 0 auto;
        }
        .pw-section label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
        }
        .pw-section input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 14px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }
        .pw-msg {
            color: #b00;
            margin-bottom: 10px;
            text-align: center;
        }
        .pw-success {
            color: green;
        }
        .save-btn {
            padding: 10px 24px;
            border-radius: 6px;
            border: none;
            background: #6884d3;
            color: #fff;
            font-weight: 500;
            cursor: pointer;
            font-size: 1rem;
        }
        .save-btn:hover {
            background: #4a5fa3;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #6884d3;
            text-decoration: underline;
            font-size: 1rem;
        }
    </style>
</head>
<body>
<div class="logo-row">
    <img src="images/logo.png" alt="Company Logo" class="logo-img">
    <span class="company-name">InnovativeTech Solutions.</span>
</div>
<div class="center-content">
    <div class="form-box">
        <h2>Change Password</h2>
        <?php if ($pw_msg): ?>
            <div class="pw-msg<?php echo (strpos($pw_msg, 'success') !== false) ? ' pw-success' : ''; ?>"><?php echo $pw_msg; ?></div>
        <?php endif; ?>
        <form method="post" class="pw-section">
            <label for="old_password">Old Password</label>
            <input type="password" name="old_password" id="old_password" required>
            <label for="new_password">New Password</label>
            <input type="password" name="new_password" id="new_password" required>
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" name="confirm_password" id="confirm_password" required>
            <button type="submit" name="change_password" class="save-btn">Change Password</button>
        </form>
        <a href="welcome.php" class="back-link">Back to Profile</a>
    </div>
</div>
</body>
</html> 