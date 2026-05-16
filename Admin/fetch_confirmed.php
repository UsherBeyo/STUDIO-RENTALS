<?php
include '../dbcon.php'; // Include database connection
require 'vendor/autoload.php'; // PHPMailer autoload
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendUpdateEmail($name, $email, $newDate, $startTime, $endTime) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username   = 'ribushvil@gmail.com';
        $mail->Password   = 'ohigkuaguyrlzjqc';
        $mail->SMTPSecure = "tls";
        $mail->Port = 587;

        $mail->setFrom('ribushvil@gmail.com', 'K2 Band Rehearsal Studios');
        $mail->addAddress($email);
        $mail->Subject = "Booking Schedule Update";

        $mail->isHTML(true);
        $mail->Body = "
            <h2>Your Booking Schedule Has Been Updated</h2>
            <p>Hello {$name},</p>
            <p>Your booking schedule has been updated to:</p>
            <ul>
                <li><strong>Date:</strong> {$newDate}</li>
                <li><strong>Start Time:</strong> {$startTime}</li>
                <li><strong>End Time:</strong> {$endTime}</li>
            </ul>
            <p>Thank you for choosing our studio!</p>
        ";

        $mail->send();
    } catch (Exception $e) {
        error_log("Email failed: {$mail->ErrorInfo}");
    }
}

$query = "SELECT * FROM bookings";
$result = $con->query($query);

echo "<h2>Confirmed Schedules</h2>";
echo "<table border='1' width='100%'>
        <thead>
            <tr>
                <th>Studio ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Date</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Total Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['studio_id']}</td>
            <td>{$row['name']}</td>
            <td>{$row['email']}</td>
            <td>{$row['booking_date']}</td>
            <td>{$row['start_time']}</td>
            <td>{$row['end_time']}</td>
            <td>{$row['total_price']} PHP</td>
            <td>
                <button onclick='showEditPopup({$row['booking_id']}, \"{$row['booking_date']}\", \"{$row['start_time']}\", \"{$row['end_time']}\")' class='btn btn-warning'>Edit</button>
                <a href='delete_booking.php?id={$row['booking_id']}' onclick='return confirm(\"Are you sure you want to delete this booking?\");' class='btn btn-danger'>Delete</a>
            </td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='8'>No confirmed schedules found.</td></tr>";
}

echo "</tbody></table>";
?>

<!-- Edit Popup Div -->
<div id="editPopup" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
    <div style="background:#fff; padding:20px; width:400px; margin:100px auto; border-radius:8px;">
        <h3>Edit Booking</h3>
        <form id="editForm" method="POST" action="process_edit_booking.php">
            <input type="hidden" id="booking_id" name="booking_id">
            <label for="booking_date">Date:</label>
            <input type="date" id="booking_date" name="booking_date" required><br><br>
            <label for="start_time">Start Time:</label>
            <input type="time" id="start_time" name="start_time" required><br><br>
            <label for="end_time">End Time:</label>
            <input type="time" id="end_time" name="end_time" required><br><br>
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <button type="button" onclick="closeEditPopup()" class="btn btn-danger">Cancel</button>
        </form>
    </div>
</div>
