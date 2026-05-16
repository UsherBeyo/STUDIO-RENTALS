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

if (isset($_GET['studio_id'])) {
    $studio_id = $_GET['studio_id'];

    // Fetch all instruments for this specific studio
    $sql = "SELECT * FROM instruments WHERE studio_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $studio_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // If no instruments are found for the studio
    if ($result->num_rows === 0) {
        echo "No instruments found for this studio.";
    }
} else {
    die("Studio ID is required.");
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Instruments</title>
    <link rel="stylesheet" href="admin_studiostyles.css">
</head>
<body>
    <h1>Edit Instruments for Studio ID: <?= htmlspecialchars($studio_id) ?></h1>

    <table border="1">
        <thead>
            <tr>
                <th>Instrument Name</th>
                <th>Instrument Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($instrument = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($instrument['instrument_name']) ?></td>
                    <td><img src="<?= htmlspecialchars($instrument['instrument_image_path']) ?>" alt="Instrument Image" width="100"></td>
                    <td>
                        <a href="edit_instrument_form.php?instrument_id=<?= $instrument['id'] ?>">Edit</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    
    <a href="../index.php?section=Studios">Back to Studios</a>
</body>
</html>

<?php $conn->close(); ?>
