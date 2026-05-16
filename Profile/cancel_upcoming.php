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
    $booking_id = $_GET['id'];

    // Fetch booking details
    $query = "SELECT name, email, booking_date, start_time, end_time FROM bookings WHERE booking_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();
    $stmt->close();

    if ($booking) {
        $booking_date = $booking['booking_date'];
        $start_time = $booking['start_time'];
        $end_time = $booking['end_time'];

        // Delete the booking
        $deleteQuery = "DELETE FROM bookings WHERE booking_id = ?";
        $stmt = $con->prepare($deleteQuery);
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        $stmt->close();

        // Check for matching waiting_bookings
        $checkQuery = "SELECT id, email, name FROM waiting_bookings WHERE booking_date = ? AND start_time = ? AND end_time = ?";
        $stmt = $con->prepare($checkQuery);
        $stmt->bind_param("sss", $booking_date, $start_time, $end_time);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($waitingUser = $result->fetch_assoc()) {
            $waiting_email = $waitingUser['email'];
            $waiting_name = $waitingUser['name'];

            // Send notification email
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

                // Optionally remove from waiting_bookings
                $deleteWaitingQuery = "DELETE FROM waiting_bookings WHERE id = ?";
                $stmtDelete = $con->prepare($deleteWaitingQuery);
                $stmtDelete->bind_param("i", $waitingUser['id']);
                $stmtDelete->execute();
                $stmtDelete->close();
            } catch (Exception $e) {
                error_log("Mailer Error: {$mail->ErrorInfo}");
            }
        }

        $_SESSION['status'] = "Upcoming reservation cancelled, and waiting users notified.";
        header("Location: user_profile.php");
        exit();
    } else {
        $_SESSION['status'] = "Reservation not found.";
        header("Location: user_profile.php");
        exit();
    }
}
?>
