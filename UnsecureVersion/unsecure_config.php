<?php
// Database configuration using environment variables for flexibility and security
$config = [
    'DB_SERVER'   => getenv('DB_SERVER') ?: '127.0.0.1',
    'DB_USERNAME' => getenv('DB_USERNAME') ?: 'root',
    'DB_PASSWORD' => getenv('DB_PASSWORD') ?: '',
    'DB_NAME'     => getenv('DB_NAME') ?: 'securedatabase'
];

// Create connection
$conn = new mysqli($config['DB_SERVER'], $config['DB_USERNAME'], $config['DB_PASSWORD'], $config['DB_NAME']);

// Check connection
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

return $config; 