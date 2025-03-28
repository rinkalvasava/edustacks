<?php
header('Content-Type: application/json');
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = $_POST['book_id'];
    $book_name = $_POST['book_name'];
    $author = $_POST['author'];
    $category = $_POST['category'];
    $isbn = $_POST['isbn'];
    $price = $_POST['price'];

    $stmt = $conn->prepare("UPDATE books SET book_name = ?, author = ?, category = ?, isbn = ?, price = ? WHERE book_id = ?");
    $stmt->bind_param("ssssdi", $book_name, $author, $category, $isbn, $price, $book_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Book updated successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update book"]);
    }

    $stmt->close();
}

$conn->close();
?>
