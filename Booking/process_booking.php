<?php
session_start();
include '../dbcon.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fetch data from the form
    $studio_id = $_POST['studio_id'];
    $booking_date = $_POST['booking_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $total_price = $_POST['total_price'];

    // Fetch user details from the session
    $user = $_SESSION['auth_user']; // Ensure 'auth_user' contains user data
    $name = $user['first_name'] . ' ' . $user['last_name'];
    $email = $user['email'];

    // File upload handling
    if (isset($_FILES['gcash_receipt']) && $_FILES['gcash_receipt']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['gcash_receipt']['tmp_name'];
        $file_name = basename($_FILES['gcash_receipt']['name']);
        $upload_dir = 'uploads/receipts/'; // Ensure this directory exists
        $receipt_path = $upload_dir . $file_name;

        // Move uploaded file to the designated directory
        if (!move_uploaded_file($file_tmp, $receipt_path)) {
            $_SESSION['status'] = "Failed to upload receipt.";
            header("Location: booking_confirmation.php");
            exit();
        }
    } else {
        $_SESSION['status'] = "No receipt uploaded or upload error.";
        header("Location: booking_confirmation.php");
        exit();
    }

    // Insert booking details into pending_bookings table
    $query = "INSERT INTO pending_bookings (studio_id, booking_date, start_time, end_time, total_price, receipt_path, name, email) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("isssdsss", $studio_id, $booking_date, $start_time, $end_time, $total_price, $receipt_path, $name, $email);

    if ($stmt->execute()) {
        $_SESSION['status'] = "Your booking has been submitted and is pending confirmation.";
        header("Location: ../index.php"); // Redirect to a success page
        exit();
    } else {
        $_SESSION['status'] = "Failed to submit booking. Please try again.";
        header("Location: booking_confirmation.php");
        exit();
    }
} else {
    $_SESSION['status'] = "Invalid access method.";
    header("Location: ../index.php");
    exit();
}
?>
