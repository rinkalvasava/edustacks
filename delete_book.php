<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

// Include database connection file
require 'conn.php';

$response = ["success" => false, "message" => "Invalid request"];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['book_id'])) {
        echo json_encode(["success" => false, "message" => "Book ID is missing"]);
        exit;
    }

    $book_id = intval($_POST['book_id']);

    if ($book_id > 0) {
        $stmt = $conn->prepare("DELETE FROM books WHERE book_id = ?");
        $stmt->bind_param("i", $book_id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $response = ["success" => true, "message" => "Book deleted successfully"];
        } else {
            $response = ["success" => false, "message" => "Failed to delete book"];
        }

        $stmt->close();
    } else {
        $response = ["success" => false, "message" => "Invalid book ID"];
    }
}

echo json_encode($response);
$conn->close();
?>
