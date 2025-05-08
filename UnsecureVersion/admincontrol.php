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

// No session or admin check (insecure)

$action_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_user'])) {
        $user_id = intval($_POST['delete_user']);
        $conn->query("DELETE FROM users WHERE id = $user_id");
        $action_msg = 'User deleted.';
    } elseif (isset($_POST['make_admin'])) {
        $user_id = intval($_POST['make_admin']);
        $conn->query("UPDATE users SET is_admin = 1 WHERE id = $user_id");
        $action_msg = 'User promoted to admin.';
    } elseif (isset($_POST['remove_admin'])) {
        $user_id = intval($_POST['remove_admin']);
        $conn->query("UPDATE users SET is_admin = 0 WHERE id = $user_id");
        $action_msg = 'User demoted from admin.';
    } elseif (isset($_POST['edit_user'])) {
        $user_id = intval($_POST['edit_user']);
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $dob = $_POST['dob'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $conn->query("UPDATE users SET fullname='$fullname', email='$email', dob='$dob', phone='$phone', address='$address' WHERE id=$user_id");
        $action_msg = 'User updated.';
    }
}

$users = [];
$result = $conn->query('SELECT id, username, fullname, email, dob, phone, address, is_admin FROM users');
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}
$result->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Controls</title>
    <link rel="stylesheet" href="/UnsecureVersion/style.css">
    <link rel="stylesheet" href="/UnsecureVersion/indexpage.css">
    <style>
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto 40px auto;
        }
        .admin-table th, .admin-table td {
            padding: 10px 14px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }
        .admin-table th {
            background: #f7f7f7;
        }
        .admin-action-btn {
            padding: 6px 14px;
            border-radius: 6px;
            border: none;
            background: #6884d3;
            color: #fff;
            font-weight: 500;
            cursor: pointer;
            margin-right: 6px;
            font-size: 0.95rem;
        }
        .admin-action-btn.delete {
            background: #d36868;
        }
        .admin-action-btn:hover {
            background: #4a5fa3;
        }
        .admin-action-btn.delete:hover {
            background: #a33a3a;
        }
        .edit-form input {
            width: 90%;
            padding: 6px;
            margin-bottom: 2px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        .action-msg {
            color: green;
            margin-bottom: 18px;
            text-align: center;
        }
        .admin-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .admin-header h2 {
            margin: 0;
        }
        .back-link {
            color: #6884d3;
            text-decoration: underline;
            font-size: 1rem;
        }
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
</head>
<body>
<div class="logo-row">
    <img src="images/logo.png" alt="Company Logo" class="logo-img">
    <span class="company-name">InnovativeTech Solutions.</span>
</div>
<div class="profile-topright">
    <a href="welcome.php" class="admin-tab">Home</a>
    <a href="admincontrol.php" class="admin-tab">Admin Controls</a>
    <img src="images/profile.png" alt="Profile" class="profile-img">
    <span class="profile-name">Admin</span>
</div>
<div class="center-content">
    <div class="form-box" style="max-width:1100px;text-align:left;">
        <div class="admin-header">
            <h2>Admin Controls</h2>
            <a href="welcome.php" class="back-link">Back to Home</a>
        </div>
        <?php if ($action_msg): ?>
            <div class="action-msg"><?php echo $action_msg; ?></div>
        <?php endif; ?>
        <table class="admin-table">
            <tr>
                <th>Username</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>DOB</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($users as $user): ?>
                <tr>
                    <form method="post" class="edit-form">
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><input type="text" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required></td>
                        <td><input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required></td>
                        <td><input type="date" name="dob" value="<?php echo htmlspecialchars($user['dob']); ?>" required></td>
                        <td><input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required></td>
                        <td><input type="text" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required></td>
                        <td><?php echo $user['is_admin'] ? 'Admin' : 'User'; ?></td>
                        <td>
                            <button type="submit" name="edit_user" value="<?php echo $user['id']; ?>" class="admin-action-btn">Save</button>
                            <?php if (!$user['is_admin']): ?>
                                <button type="submit" name="make_admin" value="<?php echo $user['id']; ?>" class="admin-action-btn">Make Admin</button>
                            <?php else: ?>
                                <button type="submit" name="remove_admin" value="<?php echo $user['id']; ?>" class="admin-action-btn">Remove Admin</button>
                            <?php endif; ?>
                            <button type="submit" name="delete_user" value="<?php echo $user['id']; ?>" class="admin-action-btn delete" onclick="return confirm('Are you sure you want to delete this user?');">Delete</button>
                        </td>
                    </form>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
</body>
</html> 