<?php
// Database connection
include 'config.php';

// Fetch all users from the database
$sql = "SELECT id, username, password, first_name, last_name, email, token_key, verified, role FROM users";
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error); // Debug query failure
}

// Check if there are results
if ($result->num_rows > 0) {
    // Output each row as an HTML table row
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['username'] . "</td>";
        echo "<td>********</td>"; // Mask password for security reasons
        echo "<td>" . $row['first_name'] . "</td>";
        echo "<td>" . $row['last_name'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['token_key'] . "</td>";
        echo "<td>" . ($row['verified'] == 1 ? 'Yes' : 'No') . "</td>";
        echo "<td>" . $row['role'] . "</td>";
        echo "<td>";
        echo "<button class='edit-btn' data-id='" . $row['id'] . "'>Edit</button>";
        echo "<button class='delete-btn' data-id='" . $row['id'] . "'>Delete</button>";
        echo "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='10'>No users found.</td></tr>";
}

$conn->close();
?>
