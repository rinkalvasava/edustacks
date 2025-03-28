<?php

include 'conn.php'; // Include database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['book_id']) && isset($_POST['student_id'])) {
        $book_id = $_POST['book_id'];
        $student_id = $_POST['student_id'];
        $issue_date = date('Y-m-d');
        $return_date = date('Y-m-d', strtotime('+14 days')); // Default return period of 14 days

        // Check if the book is available
        $check_query = "SELECT * FROM books WHERE id = ? AND status = 'available'";
        $stmt = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($stmt, "i", $book_id);
        mysqli_stmt_execute($stmt);
        $check_result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($check_result) > 0) {
            // Issue the book
            $issue_query = "INSERT INTO issued_books (book_id, student_id, issue_date, return_date) 
                            VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $issue_query);
            mysqli_stmt_bind_param($stmt, "iiss", $book_id, $student_id, $issue_date, $return_date);

            if (mysqli_stmt_execute($stmt)) {
                // Update book status to 'issued'
                $update_query = "UPDATE books SET status = 'issued' WHERE id = ?";
                $stmt = mysqli_prepare($conn, $update_query);
                mysqli_stmt_bind_param($stmt, "i", $book_id);
                mysqli_stmt_execute($stmt);

                echo "Book issued successfully. Due date: " . $return_date;
            } else {
                echo "Error issuing book: " . mysqli_error($conn);
            }
        } else {
            echo "Book is not available for issuing.";
        }
    } else {
        echo "Invalid input. Please provide both book ID and student ID.";
    }
} else {
    echo "Invalid request method.";
}

?>
