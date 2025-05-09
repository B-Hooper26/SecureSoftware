<?php
require_once __DIR__ . '/../config.php';
$config = require __DIR__ . '/../config.php';
$conn = new mysqli($config['DB_SERVER'], $config['DB_USERNAME'], $config['DB_PASSWORD'], $config['DB_NAME']);

$test_email = "notanemail";
$username = "emailvuln_" . time();
$sql = "INSERT INTO users (username, fullname, email, dob, password, phone, address, is_admin) VALUES ('$username', 'Test', '$test_email', '2000-01-01', 'test', '123', 'addr', 0)";
if ($conn->query($sql)) {
    echo "PASS: Invalid email accepted (vulnerable).\n";
    $conn->query("DELETE FROM users WHERE username = '$username'");
    exit(0);
} else {
    echo "FAIL: Invalid email rejected (not vulnerable).\n";
    exit(1);
} 