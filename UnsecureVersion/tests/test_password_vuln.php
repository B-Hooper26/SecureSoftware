<?php
require_once __DIR__ . '/../config.php';
$config = require __DIR__ . '/../config.php';
$conn = new mysqli($config['DB_SERVER'], $config['DB_USERNAME'], $config['DB_PASSWORD'], $config['DB_NAME']);

// Insert a test user with a known password
$username = 'pwtest_' . time();
$password = 'plaintextpw123';
$conn->query("INSERT INTO users (username, fullname, email, dob, password, phone, address, is_admin) VALUES ('$username', 'Test', '$username@example.com', '2000-01-01', '$password', '123', 'addr', 0)");

// Fetch the password from the database
$result = $conn->query("SELECT password FROM users WHERE username = '$username'");
$row = $result->fetch_assoc();
$db_password = $row['password'];

if ($db_password === $password) {
    echo "PASS: Passwords are stored in plain text (vulnerable).\n";
    $conn->query("DELETE FROM users WHERE username = '$username'");
    exit(0);
} else {
    echo "FAIL: Passwords are not stored in plain text.\n";
    $conn->query("DELETE FROM users WHERE username = '$username'");
    exit(1);
} 