<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'conn.php';

header("Content-Type: application/json");

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed"]));
}

// Fetch universities with additional fields
$query = "SELECT university_id, university_name, email, username, password, contact_no, location FROM universities";
$result = $conn->query($query);

if (!$result) {
    die(json_encode(["status" => "error", "message" => "Query failed: " . $conn->error]));
}

$universities = [];
while ($row = $result->fetch_assoc()) {
    $universities[] = [
        "university_id" => $row["university_id"],
        "university_name" => $row["university_name"],
        "email" => $row["email"],
        "username" => $row["username"],
        "password" => $row["password"],
        "contact_no" => (int) $row["contact_no"], // Ensuring contact_no is returned as an integer
        "location" => $row["location"]
    ];
}

echo json_encode($universities, JSON_PRETTY_PRINT);
$conn->close();
?>
