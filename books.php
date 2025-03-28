<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'conn.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if (!$conn) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

// Debugging - Check if the database connection works
$sql = "SELECT book_id, book_name, category, author, isbn, price FROM books";
$result = $conn->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(["error" => "SQL Error: " . $conn->error]);
    exit();
}

// Fetch and return books
$books = [];
while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}

http_response_code(200);
echo json_encode(["success" => true, "books" => $books], JSON_PRETTY_PRINT);

$conn->close();
?>
