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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studio_id = $_POST['studio_id'];
    $instrument_name = $_POST['instrument_name'];

    // Handle instrument image upload
    if (!empty($_FILES['instrument_image']['name'])) {
        $image_name = $_FILES['instrument_image']['name'];
        $image_tmp_name = $_FILES['instrument_image']['tmp_name'];
        $upload_dir = 'uploads/instruments/';
        $target_file = $upload_dir . basename($image_name);

        if (move_uploaded_file($image_tmp_name, $target_file)) {
            // Insert instrument into the database
            $sql = "INSERT INTO instruments (studio_id, instrument_name, instrument_image_path) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iss", $studio_id, $instrument_name, $target_file);

            if ($stmt->execute()) {
                echo "Instrument added successfully.";
            } else {
                echo "Error adding instrument: " . $stmt->error;
            }
            $stmt->close();
        } else {
            die("Failed to upload image.");
        }
    } else {
        echo "No image uploaded.";
    }
    $conn->close();
} else {
    if (isset($_GET['studio_id'])) {
        $studio_id = $_GET['studio_id'];
    } else {
        die("Studio ID is required.");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Instrument</title>
    <link rel="stylesheet" href="admin_studiostyles.css">
</head>
<body>
    <h1>Add Instrument to Studio</h1>
    <form action="add_instrument.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="studio_id" value="<?= htmlspecialchars($studio_id) ?>">

        <label for="instrument_name">Instrument Name:</label>
        <input type="text" name="instrument_name" required><br>

        <label for="instrument_image">Instrument Image:</label>
        <input type="file" name="instrument_image" accept="image/*" required><br>

        <button type="submit">Add Instrument</button>
    </form>
    <a href="../index.php?section=Studios">Back</a>
</body>
</html>
