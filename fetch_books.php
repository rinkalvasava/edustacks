<?php
header("Content-Type: application/json");
include 'conn.php'; // Include the database connection file

$sql = "SELECT book_name AS title, author, file_path FROM books"; // Fetch PDF URL
$result = $conn->query($sql);

$books = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
}

echo json_encode(["success" => true, "books" => $books]);

$conn->close();
?>
