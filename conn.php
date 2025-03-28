<?php

// Database credentials
$host = 'localhost';
$username = 'root';
$password = ''; // Replace with your actual database password
$database = 'handy_library';

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed."]));
}

// Optional: Set character encoding to UTF-8
$conn->set_charset("utf8");

?>
