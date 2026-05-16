<?php
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
