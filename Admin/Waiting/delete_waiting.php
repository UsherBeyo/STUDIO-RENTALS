<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'studio_rentals');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Delete the record from the waiting_bookings table
    $query = "DELETE FROM waiting_bookings WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['status'] = "Record successfully deleted.";
    } else {
        $_SESSION['status'] = "Failed to delete the record.";
    }

    header("Location: ../index.php");
    exit(0);
} else {
    $_SESSION['status'] = "Invalid request.";
    header("Location: ../index.php");
    exit(0);
}
?>
