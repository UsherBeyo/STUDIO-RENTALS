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

$id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM studios WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: ../index.php?section=Studios&success=Studio deleted successfully.");
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
