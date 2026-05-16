<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New User</title>
    <link rel="stylesheet" href="CRUD_styles/crud_styles.css">
</head>
<body>
    <div class="form-container">
        <h1>Create New User</h1>
        <form method="POST">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password</label>
            <input type="text" id="password" name="password" required>

            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" required>

            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="token_key">Token Key</label>
            <input type="text" id="token_key" name="token_key" required>

            <label><input type="checkbox" name="verified"> Verified</label>

            <label for="role">Role</label>
            <input type="text" id="role" name="role" required>

            <button type="submit">Create User</button>
        </form>

        <a href="index.php" class="back-button">Back to User List</a>
    </div>
</body>
</html>


<?php
// Database connection
$host = 'localhost';
$db = 'studio_rentals';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert new user
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $token_key = $_POST['token_key'];
    $verified = isset($_POST['verified']) ? 1 : 0;
    $role = $_POST['role'];

    // Insert query
    $sql = "INSERT INTO users (username, password, first_name, last_name, email, token_key, verified, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssis", $username, $password, $first_name, $last_name, $email, $token_key, $verified, $role);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error creating user: " . $conn->error;
    }
}

$conn->close();
?>
