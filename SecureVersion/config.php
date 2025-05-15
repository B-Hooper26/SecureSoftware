<?php
// Database configuration
$DB_SERVER = '127.0.0.1';
$DB_USERNAME = 'root';
$DB_PASSWORD = '';
$DB_NAME = 'securedatabase';

// Create connection
$conn = new mysqli($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);

// Check connection
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
?> 