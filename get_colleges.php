<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'conn.php';

header("Content-Type: application/json");

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed"]));
}

// Fetch colleges with necessary fields
$query = "SELECT college_id, college_name, email, username, password, contact_no, location FROM college";
$result = $conn->query($query);

if (!$result) {
    die(json_encode(["status" => "error", "message" => "Query failed: " . $conn->error]));
}

$colleges = [];
while ($row = $result->fetch_assoc()) {
    $colleges[] = [
        "college_id" => $row["college_id"],
        "college_name" => $row["college_name"],
        "email" => $row["email"],
        "username" => $row["username"],
        "password" => $row["password"],
        "contact_no" => (int) $row["contact_no"], // Ensure contact_no is an integer
        "location" => $row["location"]
    ];
}

echo json_encode($colleges, JSON_PRETTY_PRINT);
$conn->close();
?>
