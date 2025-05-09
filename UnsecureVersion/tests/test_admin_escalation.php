<?php
require_once __DIR__ . '/../config.php';
$config = require __DIR__ . '/../config.php';
$conn = new mysqli($config['DB_SERVER'], $config['DB_USERNAME'], $config['DB_PASSWORD'], $config['DB_NAME']);

$username = "adminvuln_" . time();
$conn->query("INSERT INTO users (username, fullname, email, dob, password, phone, address, is_admin) VALUES ('$username', 'Test', '$username@example.com', '2000-01-01', 'test', '123', 'addr', 0)");
$conn->query("UPDATE users SET is_admin = 1 WHERE username = '$username'");
$result = $conn->query("SELECT is_admin FROM users WHERE username = '$username'");
$row = $result->fetch_assoc();
if ($row && $row['is_admin'] == 1) {
    echo "PASS: Privilege escalation possible (vulnerable).\n";
    $conn->query("DELETE FROM users WHERE username = '$username'");
    exit(0);
} else {
    echo "FAIL: Privilege escalation not possible.\n";
    $conn->query("DELETE FROM users WHERE username = '$username'");
    exit(1);
} 