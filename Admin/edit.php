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

// Check if the form is submitted and update the user
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $token_key = $_POST['token_key'];
    $verified = isset($_POST['verified']) ? 1 : 0;
    $role = $_POST['role'];

    $sql = "UPDATE users SET username=?, password=?, first_name=?, last_name=?, email=?, token_key=?, verified=?, role=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssisi", $username, $password, $first_name, $last_name, $email, $token_key, $verified, $role, $id);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error updating user: " . $conn->error;
    }
}

// Fetch the user details to pre-fill the form
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="CRUD_Styles/crud_styles.css">
</head>
<body>
    <div class="form-container">
        <h1>Edit User</h1>
        <form method="POST">
            <!-- Hidden field for the user ID -->
            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">

            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?php echo $user['username']; ?>" required>

            <label for="password">Password</label>
            <input type="text" id="password" name="password" value="<?php echo $user['password']; ?>" required>

            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" value="<?php echo $user['first_name']; ?>" required>

            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" value="<?php echo $user['last_name']; ?>" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required>

            <label for="token_key">Token Key</label>
            <input type="text" id="token_key" name="token_key" value="<?php echo $user['token_key']; ?>" required>

            <label><input type="checkbox" name="verified" <?php echo $user['verified'] ? 'checked' : ''; ?>> Verified</label>

            <label for="role">Role</label>
            <input type="text" id="role" name="role" value="<?php echo $user['role']; ?>" required>

            <button type="submit">Update User</button>
        </form>

        <a href="index.php" class="back-button">Back to User List</a>
    </div
