<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1); 

include('conn.php');

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["status" => "error", "message" => "Invalid JSON input."]);
    exit();
}

// ✅ Required fields check
$required_fields = ['college_name', 'username', 'email', 'location', 'contact_no', 'password', 'university_id'];
foreach ($required_fields as $field) {
    if (!isset($data[$field]) || empty($data[$field])) {
        echo json_encode(["status" => "error", "message" => "Missing required field: $field"]);
        exit();
    }
}

$name = $conn->real_escape_string($data['college_name']);
$username = $conn->real_escape_string($data['username']);
$email = $conn->real_escape_string($data['email']);
$location = $conn->real_escape_string($data['location']);
$contact_no = $conn->real_escape_string($data['contact_no']);
$university_id = $conn->real_escape_string($data['university_id']);
$password = $conn->real_escape_string($data['password']); // ❌ No hashing, storing as plain text

// ✅ Check if college exists under the same university
$check_stmt = $conn->prepare("SELECT college_id FROM college WHERE college_name = ? AND university_id = ?");
$check_stmt->bind_param("si", $name, $university_id);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "College already exists under this university."]);
    exit();
}
$check_stmt->close();

// ✅ Insert college into the database (password stored as plain text)
$stmt = $conn->prepare("INSERT INTO college (college_name, username, email, location, contact_no, password, university_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssi", $name, $username, $email, $location, $contact_no, $password, $university_id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "College registered successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => "Registration failed: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
