<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include 'conn.php';

// Get JSON input
$inputJSON = file_get_contents("php://input");
$data = json_decode($inputJSON, true);

// Debugging: Log received data
file_put_contents("debug_log.txt", print_r($data, true), FILE_APPEND);

// Check if username and password are set
if (!isset($data['username']) || !isset($data['password'])) {
    echo json_encode(["success" => false, "message" => "Username and password are required"]);
    exit;
}

$username = $data['username'];
$password = $data['password'];

// Prepare SQL statement
$sql = "SELECT university_id AS id, username FROM universities WHERE username = ? AND password = ? 
        UNION 
        SELECT college_id AS id, username FROM college WHERE username = ? AND password = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $username, $password, $username, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo json_encode([
        "success" => true,
        "message" => "Login successful",
        "user_id" => $user['id'],
        "username" => $user['username']
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Invalid credentials"]);
}

$stmt->close();
$conn->close();
?>
