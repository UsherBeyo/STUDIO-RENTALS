<?php
session_start();
if (!isset($_SESSION['authenticated']) || $_SESSION['auth_user']['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'studio_rentals');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the studio data for editing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Existing studio update logic
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $max_occupancy = $_POST['max_occupancy'];

    // Update the studio details here...
    
    // Handle instrument updates (add or delete)
    if (isset($_FILES['instrument_images']) && !empty($_FILES['instrument_images']['name'][0])) {
        // Delete existing instruments if necessary (optional)
        // Insert new instruments

        foreach ($_FILES['instrument_images']['name'] as $key => $image_name) {
            $instrument_name = $_POST['instrument_names'][$key];
            $instrument_tmp_name = $_FILES['instrument_images']['tmp_name'][$key];
            $instrument_upload_dir = 'uploads/instruments/';
            $instrument_target_file = $instrument_upload_dir . basename($image_name);

            // Move uploaded instrument image
            if (move_uploaded_file($instrument_tmp_name, $instrument_target_file)) {
                $sql_instrument = "INSERT INTO instruments (studio_id, instrument_name, instrument_image_path) VALUES (?, ?, ?)";
                $stmt_instrument = $conn->prepare($sql_instrument);
                $stmt_instrument->bind_param("iss", $id, $instrument_name, $instrument_target_file);
                $stmt_instrument->execute();
            }
        }
    }

    // Execute the studio update query...
    $stmt->close();
    $conn->close();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Studio</title>
    <link rel="stylesheet" href="admin_studiostyles.css">
</head>
<body>
    <h1>Edit Studio</h1>
    <form action="edit_studio.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= htmlspecialchars($studio['id']) ?>">
        <label>Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($studio['name']) ?>" required><br>
        <label>Description:</label>
        <textarea name="description" required><?= htmlspecialchars($studio['description']) ?></textarea><br>
        <label>Price (PHP/hour):</label>
        <input type="number" name="price" step="0.01" value="<?= htmlspecialchars($studio['price']) ?>" required><br>
        <label>Max Occupancy:</label>
        <input type="number" name="max_occupancy" value="<?= htmlspecialchars($studio['max_occupancy']) ?>" required><br>
        <label>Image:</label>
        <input type="file" name="image" accept="image/*"><br>
        <button type="submit">Update Studio</button>
    </form>
    <a href="../index.php?section=Studios">Back</a>
</body>
</html>
