<?php
session_start();
include '../dbcon.php'; // Database connection

if (!isset($_SESSION['authenticated'])) {
    $_SESSION['status'] = "You need to log in to edit your profile.";
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['auth_user']['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize inputs
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    // Update query
    if ($password) {
        $query = "UPDATE users SET first_name = ?, last_name = ?, username = ?, email = ?, password = ? WHERE id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("sssssi", $first_name, $last_name, $username, $email, $password, $user_id);
    } else {
        $query = "UPDATE users SET first_name = ?, last_name = ?, username = ?, email = ? WHERE id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("ssssi", $first_name, $last_name, $username, $email, $user_id);
    }

    // Execute the query
    if ($stmt->execute()) {
        $_SESSION['status'] = "Profile updated successfully!";
    } else {
        $_SESSION['status'] = "Failed to update profile. Please try again.";
    }
    $stmt->close();
}

// Redirect back to the profile page
header("Location: user_profile.php");
exit();
?>
