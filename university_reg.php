<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

include 'conn.php';

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['university_name'], $data['email'], $data['username'], $data['location'], $data['password'], $data['contact_no'], $data['access_id'], $data['access_point'])) {
    $university_name = mysqli_real_escape_string($conn, $data['university_name']);
    $email = mysqli_real_escape_string($conn, $data['email']);
    $contact_no = mysqli_real_escape_string($conn, $data['contact_no']);
    $username = mysqli_real_escape_string($conn, $data['username']);
    $location = mysqli_real_escape_string($conn, $data['location']);
    $password = mysqli_real_escape_string($conn, $data['password']);
    $access_id = mysqli_real_escape_string($conn, $data['access_id']);
    $access_point = mysqli_real_escape_string($conn, $data['access_point']);

    // Check if Email, Username, or Contact Number already exists
    $check_query = $conn->prepare("SELECT university_id FROM universities WHERE email = ? OR username = ? OR contact_no = ?");
    $check_query->bind_param("sss", $email, $username, $contact_no);
    $check_query->execute();
    $check_query->store_result();

    if ($check_query->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Email, Username, or Contact Number already exists"]);
    } else {
        // Ensure the `universities` table has the same columns as below
        $query = $conn->prepare("INSERT INTO universities (university_name, email, contact_no, username, location, password, access_id, access_point) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $query->bind_param("ssssssis", $university_name, $email, $contact_no, $username, $location, $password, $access_id, $access_point);

        if ($query->execute()) {
            echo json_encode(["status" => "success", "message" => "University registered successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Registration failed: " . $query->error]);
        }
        $query->close();
    }

    $check_query->close();
} else {
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
}

$conn->close();
?>
