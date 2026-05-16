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
    // Existing studio details
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $max_occupancy = $_POST['max_occupancy'];

    // Initialize image path with a default value if no image is uploaded
    $image_path = 'default.jpg';  // Set a default image if none is uploaded

    // Handle studio image upload if an image is provided
    if (!empty($_FILES['image']['name'])) {
        $image_name = $_FILES['image']['name'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $upload_dir = 'uploads/';
        $target_file = $upload_dir . basename($image_name);

        // Move uploaded file to target directory
        if (move_uploaded_file($image_tmp_name, $target_file)) {
            $image_path = $target_file;  // Update the image path to the uploaded file
        } else {
            die("Failed to upload image.");
        }
    }

    // Insert studio details into the database (including the image path)
    $sql = "INSERT INTO studios (name, description, price, max_occupancy, image_path) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdis", $name, $description, $price, $max_occupancy, $image_path);
    $stmt->execute();
    
    // Get the inserted studio ID
    $studio_id = $conn->insert_id;

    // Insert instruments if any
    if (isset($_FILES['instrument_images']) && !empty($_FILES['instrument_images']['name'][0])) {
        foreach ($_FILES['instrument_images']['name'] as $key => $image_name) {
            $instrument_name = $_POST['instrument_names'][$key];
            $instrument_tmp_name = $_FILES['instrument_images']['tmp_name'][$key];
            $instrument_upload_dir = 'uploads/instruments/';
            $instrument_target_file = $instrument_upload_dir . basename($image_name);

            // Move uploaded instrument image
            if (move_uploaded_file($instrument_tmp_name, $instrument_target_file)) {
                $sql_instrument = "INSERT INTO instruments (studio_id, instrument_name, instrument_image_path) VALUES (?, ?, ?)";
                $stmt_instrument = $conn->prepare($sql_instrument);
                $stmt_instrument->bind_param("iss", $studio_id, $instrument_name, $instrument_target_file);
                $stmt_instrument->execute();
            }
        }
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Studio</title>
    <link rel="stylesheet" href="admin_studiostyles.css">
</head>
<body>
    <h1>Add New Studio</h1>
    <form action="add_studio.php" method="POST" enctype="multipart/form-data">
        <label for="studio_name">Studio Name</label>
        <input type="text" name="name" id="name" required>
        
        <label for="description">Description</label>
        <textarea name="description" id="description" rows="4" required></textarea>
        
        <label for="price">Price Per Hour</label>
        <input type="number" name="price" id="price" step="0.01" required>
        
        <label for="max_occupancy">Max Occupancy</label>
        <input type="number" name="max_occupancy" id="max_occupancy" required>

        <label for="image">Upload Image</label>
        <input type="file" name="image" id="image" accept="image/*">
        
        <button type="submit">Add Studio</button>
    </form>
    <a href="../index.php?section=Studios">Back</a>
</body>
</html>
