<?php
include 'conn.php'; // Include database connection

// Allowable file extensions
$allowed_extensions = ['pdf', 'ppt', 'png', 'jpg', 'jpeg'];
$upload_dir = "uploads/"; // Directory where files will be stored

// Ensure the upload directory exists
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Check if request is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bookName = $_POST['book_name'] ?? '';
    $author = $_POST['author'] ?? '';
    $category = $_POST['category'] ?? '';
    $isbn = $_POST['isbn'] ?? '';
    $price = $_POST['price'] ?? '';
    $filePath = '';

    // Validate required fields
    if (empty($bookName) || empty($author) || empty($category) || empty($isbn) || empty($price)) {
        echo json_encode(["success" => false, "message" => "All fields are required"]);
        exit();
    }

    // Handle file upload
    if (!empty($_FILES['file']['name'])) {
        $file_name = basename($_FILES['file']['name']);
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_size = $_FILES['file']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Validate file extension
        if (!in_array($file_ext, $allowed_extensions)) {
            echo json_encode(["success" => false, "message" => "Invalid file type"]);
            exit();
        }

        // Validate file size (Max 10MB)
        if ($file_size > 10 * 1024 * 1024) {
            echo json_encode(["success" => false, "message" => "File size should not exceed 10MB"]);
            exit();
        }

        // Generate a unique file name
        $newFileName = uniqid("book_", true) . "." . $file_ext;
        $filePath = $upload_dir . $newFileName;

        // ✅ Ensure the uploads directory exists before moving the file
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Move uploaded file to the directory
        if (!move_uploaded_file($file_tmp, $filePath)) {
            echo json_encode(["success" => false, "message" => "Error uploading file"]);
            exit();
        }
    }

    // Insert data into database
    $stmt = $conn->prepare("INSERT INTO books (book_name, author, category, isbn, price, file_path) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssds", $bookName, $author, $category, $isbn, $price, $filePath);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Book added successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error adding book: " . $stmt->error]);
    }

    $stmt->close();
}

$conn->close();
?>
