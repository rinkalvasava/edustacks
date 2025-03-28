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

// Load PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';




if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'] ?? '';

    if (empty($email)) {
        die(json_encode(["status" => "error", "message" => "Email is required"]));
    }

    // Generate OTP
    $otp = rand(100000, 999999);

    // Store OTP in database
    $stmt = $conn->prepare("INSERT INTO otp_codes (email, otp, created_at) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE otp=?, created_at=NOW()");

    if (!$stmt) {
        die(json_encode(["status" => "error", "message" => "SQL error: " . $conn->error]));
    }

    $stmt->bind_param("sii", $email, $otp, $otp);

    if ($stmt->execute()) {
        // Debugging: Check if data is actually inserted
        if ($stmt->affected_rows > 0) {
            error_log("OTP stored successfully for email: $email");
        } else {
            error_log("Failed to store OTP for email: $email");
        }

        // Send OTP via Email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'technoguideinfosoft.hr@gmail.com';
            $mail->Password = 'abbz bowk ufpr zhkj';  // Use App Password if needed
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('technoguideinfosoft.hr@gmail.com', 'Handy Library');
            $mail->addAddress($email);
            $mail->Subject = "Your OTP Code";
            $mail->Body = "Your OTP code is: $otp";

            if ($mail->send()) {
                echo json_encode(["status" => "success", "message" => "OTP sent to your email"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to send OTP email"]);
            }
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => "Could not send OTP. Error: " . $mail->ErrorInfo]);
        }
    } else {
        error_log("Database Insert Error: " . $stmt->error);
        echo json_encode(["status" => "error", "message" => "Failed to store OTP in database"]);
    }

    $stmt->close();
}
$conn->close();
?>
