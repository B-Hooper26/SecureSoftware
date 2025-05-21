<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
$is_admin = $_SESSION['is_admin'] ?? 0;
$username = htmlspecialchars($_SESSION['username']);
$user_id = $_SESSION['user_id'];


require_once 'secure_config.php';


// Fetch user details
$stmt = $conn->prepare('SELECT username, fullname, email, dob, phone, address, is_admin FROM users WHERE id = ?');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($db_username, $fullname, $email, $dob, $phone, $address, $db_is_admin);
$stmt->fetch();
$stmt->close();

// Handle update
$update_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $new_fullname = trim($_POST['fullname']);
    $new_email = trim($_POST['email']);
    $new_dob = trim($_POST['dob']);
    $new_phone = trim($_POST['phone']);
    $new_address = trim($_POST['address']);
    $stmt = $conn->prepare('UPDATE users SET fullname=?, email=?, dob=?, phone=?, address=? WHERE id=?');
    $stmt->bind_param('sssssi', $new_fullname, $new_email, $new_dob, $new_phone, $new_address, $user_id);
    if ($stmt->execute()) {
        $update_msg = 'Profile updated successfully!';
        $fullname = $new_fullname;
        $email = $new_email;
        $dob = $new_dob;
        $phone = $new_phone;
        $address = $new_address;
    } else {
        $update_msg = 'Update failed. Please try again.';
    }
    $stmt->close();
}

// Show password change success message if redirected
$pw_success = isset($_GET['pwmsg']) && $_GET['pwmsg'] === 'success';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="/SecureVersion/style.css">
    <link rel="stylesheet" href="/SecureVersion/indexpage.css">
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
        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto;
        }
        .user-table th, .user-table td {
            padding: 10px 18px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }
        .user-table th {
            background: #f7f7f7;
        }
        .edit-btn, .save-btn {
            padding: 6px 18px;
            border-radius: 6px;
            border: none;
            background: #6884d3;
            color: #fff;
            font-weight: 500;
            cursor: pointer;
            margin-top: 10px;
        }
        .edit-btn:hover, .save-btn:hover {
            background: #4a5fa3;
        }
        .update-msg {
            color: green;
            margin-bottom: 10px;
            text-align: center;
        }
        .pw-success {
            color: green;
            margin-bottom: 10px;
            text-align: center;
        }
        .change-pw-btn {
            margin-top: 30px;
            padding: 10px 24px;
            border-radius: 6px;
            border: none;
            background: #6884d3;
            color: #fff;
            font-weight: 500;
            cursor: pointer;
            font-size: 1rem;
        }
        .change-pw-btn:hover {
            background: #4a5fa3;
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
</head>
<body>
<div class="logo-row">
    <img src="images/logo.png" alt="Company Logo" class="logo-img">
    <span class="company-name">InnovativeTech Solutions.</span>
</div>
<div class="profile-topright">
    <?php if ($is_admin): ?>
        <a href="welcome.php" class="admin-tab">Home</a>
        <a href="admincontrol.php" class="admin-tab">Admin Controls</a>
    <?php endif; ?>
    <img src="images/profile.png" alt="Profile" class="profile-img">
    <span class="profile-name"><?php echo $is_admin ? 'Admin' : $db_username; ?></span>
</div>
<div class="center-content">
    <div class="form-box" style="text-align:center;max-width:600px;">
        <?php if ($update_msg): ?>
            <div class="update-msg"><?php echo $update_msg; ?></div>
        <?php endif; ?>
        <?php if ($pw_success): ?>
            <div class="pw-success">Password changed successfully!</div>
        <?php endif; ?>
        <form method="post" id="profileForm">
            <table class="user-table">
                <tr><th>Username</th><td><?php echo htmlspecialchars($db_username); ?></td></tr>
                <tr><th>Full Name</th><td><input type="text" name="fullname" value="<?php echo htmlspecialchars($fullname); ?>" required></td></tr>
                <tr><th>Email</th><td><input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required></td></tr>
                <tr><th>Date of Birth</th><td><input type="date" name="dob" value="<?php echo htmlspecialchars($dob); ?>" required></td></tr>
                <tr><th>Phone</th><td><input type="text" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required></td></tr>
                <tr><th>Address</th><td><input type="text" name="address" value="<?php echo htmlspecialchars($address); ?>" required></td></tr>
                <tr><th>Role</th><td><?php echo $db_is_admin ? 'Admin' : 'User'; ?></td></tr>
            </table>
            <button type="submit" name="update" class="save-btn">Save Changes</button>
        </form>
        <form action="change_password.php" method="get">
            <button type="submit" class="change-pw-btn">Change Password</button>
        </form>
        <a href="logout.php" style="display:inline-block;margin-top:20px;">Logout</a>
    </div>
</div>
</body>
</html> 