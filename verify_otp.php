<?php
include('conn.php');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Handle OTP verification
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'] ?? '';
    $otp = $_POST['otp'] ?? '';

    // Check if email and OTP are provided
    if (empty($email) || empty($otp)) {
        die(json_encode(["status" => "error", "message" => "Email and OTP are required"]));
    }

    // Check if OTP exists and is valid (not expired)
    $stmt = $conn->prepare("SELECT otp, created_at FROM otp_codes WHERE email = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(["status" => "error", "message" => "Invalid email or OTP"]);
        exit();
    }

    $row = $result->fetch_assoc();
    $storedOtp = $row['otp'];
    $createdAt = strtotime($row['created_at']);
    $currentTime = time();
    $otpValidity = 300; // OTP valid for 5 minutes (300 seconds)

    if ($otp == $storedOtp) {
        if (($currentTime - $createdAt) > $otpValidity) {
            echo json_encode(["status" => "error", "message" => "OTP expired"]);
        } else {
            echo json_encode(["status" => "success", "message" => "OTP verified successfully"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Incorrect OTP"]);
    }

    $stmt->close();
}
$conn->close();
?>
