<?php
include '../dbcon.php';
include 'email_functions.php'; // Include fetch_confirmed.php for sendUpdateEmail()

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $booking_id = $_POST['booking_id'];
    $new_date = $_POST['booking_date'];
    $new_start_time = $_POST['start_time'];
    $new_end_time = $_POST['end_time'];

    // Fetch existing user details
    $query = "SELECT name, email FROM bookings WHERE booking_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        $name = $row['name'];
        $email = $row['email'];

        // Update booking
        $update_query = "UPDATE bookings SET booking_date = ?, start_time = ?, end_time = ? WHERE booking_id = ?";
        $stmt = $con->prepare($update_query);
        $stmt->bind_param("sssi", $new_date, $new_start_time, $new_end_time, $booking_id);

        if ($stmt->execute()) {
            // Send email notification
            sendUpdateEmail($name, $email, $new_date, $new_start_time, $new_end_time);
            header("Location: index.php");
        } else {
            header("Location: fetch_confirmed.php?status=error");
        }
    } else {
        echo "Booking record not found!";
    }
}
?>
