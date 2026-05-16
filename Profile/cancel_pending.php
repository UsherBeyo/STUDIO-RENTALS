<?php
session_start();
include '../dbcon.php'; // Database connection
require 'vendor/autoload.php'; // Path to PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['authenticated'])) {
    header("Location: ../index.php");
    exit();
}

if (isset($_GET['id'])) {
    $pending_id = $_GET['id'];

    // Fetch the pending booking details
    $query = "SELECT name, email, booking_date, start_time, end_time FROM pending_bookings WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $pending_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $pendingBooking = $result->fetch_assoc();
    $stmt->close();

    if ($pendingBooking) {
        $booking_date = $pendingBooking['booking_date'];
        $start_time = $pendingBooking['start_time'];
        $end_time = $pendingBooking['end_time'];

        // Delete the pending reservation
        $deleteQuery = "DELETE FROM pending_bookings WHERE id = ?";
        $stmt = $con->prepare($deleteQuery);
        $stmt->bind_param("i", $pending_id);
        $stmt->execute();
        $stmt->close();

        // Check for users in waiting_bookings table with a matching schedule
        $checkQuery = "SELECT id, email, name FROM waiting_bookings WHERE booking_date = ? AND start_time = ? AND end_time = ?";
        $stmt = $con->prepare($checkQuery);
        $stmt->bind_param("sss", $booking_date, $start_time, $end_time);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($waitingUser = $result->fetch_assoc()) {
            $waiting_email = $waitingUser['email'];
            $waiting_name = $waitingUser['name'];

            // Send email to notify the waiting user
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'ribushvil@gmail.com';
                $mail->Password = 'ohigkuaguyrlzjqc';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('ribushvil@gmail.com', 'K2 Band Rehearsal Studios');
                $mail->addAddress($waiting_email, $waiting_name);

                $mail->isHTML(true);
                $mail->Subject = "Your Awaited Schedule is Now Available!";
                $mail->Body = "<h3>Dear $waiting_name,</h3>
                    <p>Your awaited schedule for <strong>$booking_date</strong> from <strong>$start_time</strong> to <strong>$end_time</strong> is now available!</p>
                    <p>Please visit our website to confirm your reservation as soon as possible.</p>
                    <br><p>Regards,<br>K2 Band Rehearsal Studios</p>";

                $mail->send();

                // Optionally remove them from waiting_bookings after notification
                $deleteWaitingQuery = "DELETE FROM waiting_bookings WHERE id = ?";
                $stmtDelete = $con->prepare($deleteWaitingQuery);
                $stmtDelete->bind_param("i", $waitingUser['id']);
                $stmtDelete->execute();
                $stmtDelete->close();
            } catch (Exception $e) {
                error_log("Mailer Error: {$mail->ErrorInfo}");
            }
        }

        $stmt->close();
        $_SESSION['status'] = "Pending reservation cancelled, and waiting users notified.";
        header("Location: user_profile.php");
        exit();
    } else {
        $_SESSION['status'] = "Pending reservation not found.";
        header("Location: user_profile.php");
        exit();
    }
}
?>
