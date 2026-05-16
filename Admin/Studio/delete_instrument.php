<?php
session_start();
if (!isset($_SESSION['authenticated']) || $_SESSION['auth_user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'studio_rentals');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['instrument_id'])) {
    $instrument_id = $_GET['instrument_id'];

    // Fetch instrument details to get the image path
    $sql = "SELECT * FROM instruments WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $instrument_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $instrument = $result->fetch_assoc();

    if ($instrument) {
        // Delete instrument image if exists
        if (file_exists($instrument['instrument_image_path'])) {
            unlink($instrument['instrument_image_path']);
        }

        // Delete the instrument from the database
        $sql_delete = "DELETE FROM instruments WHERE id = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("i", $instrument_id);
        if ($stmt_delete->execute()) {
            echo "Instrument deleted successfully.";
        } else {
            echo "Error deleting instrument: " . $stmt_delete->error;
        }
    } else {
        echo "Instrument not found.";
    }

    $stmt->close();
    $stmt_delete->close();
    $conn->close();
} else {
    echo "Instrument ID is required.";
}
?>

<a href="index.php?section=Studios">Back</a>
