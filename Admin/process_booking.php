<?php
session_start();
include '../dbcon.php';
require 'vendor/autoload.php'; // Include PHPMailer library

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendEmailNotification($email, $subject, $body)
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ribushvil@gmail.com'; // Replace with your email
        $mail->Password = 'ohigkuaguyrlzjqc';   // Replace with your app password
        $mail->SMTPSecure = "tls";
        $mail->Port = 587;

        $mail->setFrom('ribushvil@gmail.com', 'K2 Band Rehearsal Studios');
        $mail->addAddress($email);
        $mail->Subject = $subject;
        $mail->isHTML(true);
        $mail->Body = $body;

        $mail->send();
    } catch (Exception $e) {
        error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }
}

if (isset($_POST['accept']) || isset($_POST['decline'])) {
    $booking_id = $_POST['booking_id'];

    // Fetch pending booking details
    $fetch_query = "SELECT * FROM pending_bookings WHERE id = ?";
    $stmt = $con->prepare($fetch_query);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $studio_id = $row['studio_id'];
        $name = $row['name'];
        $email = $row['email'];
        $booking_date = $row['booking_date'];
        $start_time = $row['start_time'];
        $end_time = $row['end_time'];
        $total_price = $row['total_price'];
        $receipt_path = $row['receipt_path']; // Added receipt path

        if (isset($_POST['accept'])) {
            // Insert into bookings table with receipt_path
            $insert_query = "INSERT INTO bookings (studio_id, name, email, booking_date, start_time, end_time, total_price, receipt_path) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = $con->prepare($insert_query);
            $stmt_insert->bind_param("isssssss", $studio_id, $name, $email, $booking_date, $start_time, $end_time, $total_price, $receipt_path);

            if ($stmt_insert->execute()) {
                // Delete from pending_bookings
                $delete_query = "DELETE FROM pending_bookings WHERE id = ?";
                $stmt_delete = $con->prepare($delete_query);
                $stmt_delete->bind_param("i", $booking_id);
                $stmt_delete->execute();

                // Send confirmation email
                $subject = "Your Booking is Confirmed!";
                $body = "
                    <h2>Booking Confirmation</h2>
                    <p>Hi $name,</p>
                    <p>Your booking has been confirmed!</p>
                    <p><strong>Studio:</strong> $studio_id</p>
                    <p><strong>Date:</strong> $booking_date</p>
                    <p><strong>Time:</strong> $start_time - $end_time</p>
                    <p><strong>Total Price:</strong> $total_price PHP</p>
                    <p>Thank you for booking with K2 Band Rehearsal Studios!</p>
                ";
                sendEmailNotification($email, $subject, $body);

                $_SESSION['status'] = "Booking accepted and moved to confirmed schedules.";
            } else {
                $_SESSION['status'] = "Failed to accept booking.";
            }
        } elseif (isset($_POST['decline'])) {
            // Delete from pending_bookings
            $delete_query = "DELETE FROM pending_bookings WHERE id = ?";
            $stmt_delete = $con->prepare($delete_query);
            $stmt_delete->bind_param("i", $booking_id);

            if ($stmt_delete->execute()) {
                // Send decline email
                $subject = "Your Booking Request was Declined";
                $body = "
                    <h2>Booking Declined</h2>
                    <p>Hi $name,</p>
                    <p>We regret to inform you that your booking request has been declined.</p>
                    <p><strong>Studio:</strong> $studio_id</p>
                    <p><strong>Date:</strong> $booking_date</p>
                    <p><strong>Time:</strong> $start_time - $end_time</p>
                    <p>Feel free to try booking another time slot.</p>
                    <p>Thank you for choosing K2 Band Rehearsal Studios!</p>
                ";
                sendEmailNotification($email, $subject, $body);

                $_SESSION['status'] = "Booking declined and removed from pending schedules.";
            } else {
                $_SESSION['status'] = "Failed to decline booking.";
            }
        }
    } else {
        $_SESSION['status'] = "Booking record not found.";
    }

    header("Location: ../index.php");
    exit();
}
?>
