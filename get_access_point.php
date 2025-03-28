<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include 'conn.php';

$query = "SELECT access_id, access_point FROM access_point";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $accessPoints = [];
    while ($row = $result->fetch_assoc()) {
        $accessPoints[] = [
            "access_id" => $row["access_id"],
            "access_point" => $row["access_point"]
        ];
    }
    echo json_encode(["status" => "success", "access_point" => $accessPoints]);
} else {
    echo json_encode(["status" => "error", "message" => "No access points found"]);
}

$conn->close();
?>
