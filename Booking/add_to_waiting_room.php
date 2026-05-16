<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'studio_rentals');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate incoming data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $studio_id = $_POST['studio_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $booking_date = $_POST['booking_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    // Insert into waiting_bookings table
    $query = "INSERT INTO waiting_bookings (studio_id, name, email, booking_date, start_time, end_time) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssss", $studio_id, $name, $email, $booking_date, $start_time, $end_time);

    if ($stmt->execute()) {
        // Set a session message on success
        $_SESSION['status'] = "Waiting Room success! We're going to send you an email notification if ever someone cancels on your wanted schedule.";
        header("Location: ../index.php"); // Redirect back to booking page
        exit(0);
    } else {
        $_SESSION['status'] = "Failed to add to the waiting room. Please try again.";
        header("Location: ../index.php");
        exit(0);
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>
