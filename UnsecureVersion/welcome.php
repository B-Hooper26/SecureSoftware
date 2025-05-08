<?php
require_once 'config.php';
$config = require 'config.php';
$conn = new mysqli(
    $config['DB_SERVER'],
    $config['DB_USERNAME'],
    $config['DB_PASSWORD'],
    $config['DB_NAME']
);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// No session check (insecure)
$user_id = $_GET['id'] ?? 1; // Default to user 1 if not set (very insecure)

// Fetch user details (vulnerable to SQL injection)
$sql = "SELECT username, fullname, email, dob, password, phone, address, is_admin FROM users WHERE id = $user_id";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

$update_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $dob = $_POST['dob'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $sql = "UPDATE users SET fullname='$fullname', email='$email', dob='$dob', password='$password', phone='$phone', address='$address' WHERE id=$user_id";
    if ($conn->query($sql)) {
        $update_msg = 'Profile updated!';
        // Refresh user data
        $result = $conn->query("SELECT username, fullname, email, dob, password, phone, address, is_admin FROM users WHERE id = $user_id");
        $user = $result->fetch_assoc();
    } else {
        $update_msg = 'Update failed.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="/UnsecureVersion/style.css">
    <link rel="stylesheet" href="/UnsecureVersion/indexpage.css">
</head>
<body>
<div class="logo-row">
    <img src="images/logo.png" alt="Company Logo" class="logo-img">
    <span class="company-name">InnovativeTech Solutions.</span>
</div>
<div class="profile-topright">
    <a href="welcome.php" class="admin-tab">Home</a>
    <?php if ($user['is_admin']): ?>
        <a href="admincontrol.php" class="admin-tab">Admin Controls</a>
    <?php endif; ?>
    <img src="images/profile.png" alt="Profile" class="profile-img">
    <span class="profile-name"><?php echo $user['is_admin'] ? 'Admin' : htmlspecialchars($user['username']); ?></span>
</div>
<div class="center-content">
    <div class="form-box" style="text-align:center;max-width:600px;">
        <?php if ($update_msg): ?>
            <div style="color:green; margin-bottom:10px;"> <?php echo $update_msg; ?> </div>
        <?php endif; ?>
        <form method="post">
            <table class="user-table">
                <tr><th>Username</th><td><?php echo htmlspecialchars($user['username']); ?></td></tr>
                <tr><th>Full Name</th><td><input type="text" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>"></td></tr>
                <tr><th>Email</th><td><input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"></td></tr>
                <tr><th>Date of Birth</th><td><input type="date" name="dob" value="<?php echo htmlspecialchars($user['dob']); ?>"></td></tr>
                <tr><th>Password</th><td><input type="text" name="password" value="<?php echo htmlspecialchars($user['password']); ?>"></td></tr>
                <tr><th>Phone</th><td><input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>"></td></tr>
                <tr><th>Address</th><td><input type="text" name="address" value="<?php echo htmlspecialchars($user['address']); ?>"></td></tr>
                <tr><th>Role</th><td><?php echo $user['is_admin'] ? 'Admin' : 'User'; ?></td></tr>
            </table>
            <button type="submit" name="update" class="save-btn">Save Changes</button>
        </form>
        <a href="logout.php" style="display:inline-block;margin-top:20px;">Logout</a>
    </div>
</div>
<style>
.profile-topright {
    position: fixed;
    top: 24px;
    right: 40px;
    display: flex;
    align-items: center;
    z-index: 101;
}
.profile-img {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 16px;
    border: 2px solid #ccc;
}
.profile-name {
    font-size: 1.2rem;
    font-weight: 500;
    color: #222;
}
.admin-tab {
    display: inline-block;
    margin-right: 18px;
    padding: 8px 18px;
    background: #f7f7f7;
    color: #222;
    border-radius: 6px;
    font-weight: 500;
    text-decoration: none;
    font-size: 1rem;
    border: 1px solid #ccc;
    transition: background 0.2s, color 0.2s;
}
.admin-tab:hover {
    background: #6884d3;
    color: #fff;
    border-color: #6884d3;
}
</style>
</body>
</html> 