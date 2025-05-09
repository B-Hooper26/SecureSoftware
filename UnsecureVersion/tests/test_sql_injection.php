<?php
require_once __DIR__ . '/../config.php';
$config = require __DIR__ . '/../config.php';
$conn = new mysqli($config['DB_SERVER'], $config['DB_USERNAME'], $config['DB_PASSWORD'], $config['DB_NAME']);

// Insert a test user
$username = 'sqlinj_' . time();
$password = 'sqltestpw';
$conn->query("INSERT INTO users (username, fullname, email, dob, password, phone, address, is_admin) VALUES ('$username', 'Test', '$username@example.com', '2000-01-01', '$password', '123', 'addr', 0)");

// Attempt SQL injection
$sql = "SELECT id FROM users WHERE username = '' OR 1=1 -- ' OR email = '' OR 1=1 -- ' OR password = 'abc'";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    echo "PASS: SQL injection possible (vulnerable).\n";
    $conn->query("DELETE FROM users WHERE username = '$username'");
    exit(0);
} else {
    echo "FAIL: SQL injection not possible (not vulnerable).\n";
    $conn->query("DELETE FROM users WHERE username = '$username'");
    exit(1);
} 