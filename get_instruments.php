<?php
$conn = new mysqli('localhost', 'root', '', 'studio_rentals');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['studio_id'])) {
    $studioId = (int) $_GET['studio_id'];

    $query = "SELECT * FROM instruments WHERE studio_id = $studioId";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        echo "<h2>Instruments</h2><ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li><img src='Admin/Studio/" . htmlspecialchars($row['instrument_image_path']) . "' alt='" . htmlspecialchars($row['instrument_name']) . "' style='width: 200px; height: 200px;'> " . htmlspecialchars($row['instrument_name']) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No instruments available for this studio.</p>";
    }
} else {
    echo "<p>Invalid studio ID.</p>";
}

$conn->close();
?>
