<?php
include '../dbcon.php'; // Include database connection

// Fetch pending bookings along with studio name
$query = "SELECT pb.*, s.name AS studio_name 
          FROM pending_bookings pb
          INNER JOIN studios s ON pb.studio_id = s.id";

$result = $con->query($query);

echo "<h2>Pending Schedules</h2>";
echo "<table border='1' width='100%'>
        <thead>
            <tr>
                <th>Studio ID</th>
                <th>Studio Name</th>
                <th>Name</th>
                <th>Email</th>
                <th>Date</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Total Price</th>
                <th>Receipt</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['studio_id']}</td>
                <td>{$row['studio_name']}</td>
                <td>{$row['name']}</td>
                <td>{$row['email']}</td>
                <td>{$row['booking_date']}</td>
                <td>{$row['start_time']}</td>
                <td>{$row['end_time']}</td>
                <td>{$row['total_price']} PHP</td>
                <td><a href='../Booking/{$row['receipt_path']}' target='_blank'>View Receipt</a></td>
                <td>
                    <form method='POST' action='process_booking.php' style='display:inline;'>
                        <input type='hidden' name='booking_id' value='{$row['id']}'>
                        <button type='submit' name='accept' class='btn btn-success'>Accept</button>
                        <button type='submit' name='decline' class='btn btn-danger'>Decline</button>
                    </form>
                </td>
            </tr>";
    }
} else {
    echo "<tr><td colspan='10'>No pending schedules found.</td></tr>";
}

echo "</tbody></table>";
?>
