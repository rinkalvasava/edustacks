<?php
// Start session
session_start();

// Include the database connection file
include('conn.php');

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve the form data
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Basic validation
    if (!empty($username) && !empty($password)) {
        // SQL query to check if the username exists in the database
        $query = "SELECT * FROM students WHERE username = '$username'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            // Fetch user data
            $user = mysqli_fetch_assoc($result);

            // Check if the password matches
            if ($password == $user['password']) {
                // Set session variable for logged-in user
                $_SESSION['username'] = $username;
                echo "Login successful!"; // You can redirect or show a success message here
            } else {
                // Invalid password
                echo "Invalid username or password!";
            }
        } else {
            // Invalid username
            echo "Invalid username or password!";
        }
    } else {
        // Missing fields
        echo "Both fields are required!";
    }
}

// Close the database connection
mysqli_close($conn);
?>
