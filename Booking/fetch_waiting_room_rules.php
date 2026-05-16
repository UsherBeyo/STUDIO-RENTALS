<?php
$conn = new mysqli('localhost', 'root', '', 'studio_rentals');

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$query = "SELECT description FROM waiting LIMIT 1";
$result = $conn->query($query);

if ($result && $row = $result->fetch_assoc()) {
    echo nl2br(htmlspecialchars($row['description']));
} else {
    echo "No rules are available at this time.";
}

$conn->close();
?>