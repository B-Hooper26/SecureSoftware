<?php
// Database configuration
$config = [
//UNCOMENT THIS TO DO SECURITY TESTS AND COMMENT THE CONFIG CODE BELOW IT
    'DB_SERVER' => '127.0.0.1',
    'DB_USERNAME' => 'root', 
    'DB_PASSWORD' => 'root',
    'DB_NAME' => 'securedatabase'
];

//COMMENT THIS OUT WHEN DOING SECURITY TESTS
//     'DB_SERVER' => '127.0.0.1',
//     'DB_USERNAME' => 'root',
//     'DB_PASSWORD' => '', // <--- empty string
//     'DB_NAME' => 'securedatabase'
// ];

// Create connection
$conn = new mysqli($config['DB_SERVER'], $config['DB_USERNAME'], $config['DB_PASSWORD'], $config['DB_NAME']);

// Check connection
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

return $config;
?> 