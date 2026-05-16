<?php
session_start();
include('dbcon.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';

function sendemail_verify($fname, $email, $token_key)
{
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->SMTPAuth = true;

    $mail->Host = 'smtp.gmail.com';
    $mail->Username   = 'ribushvil@gmail.com';
    $mail->Password   = 'ohigkuaguyrlzjqc';

    $mail->SMTPSecure = "tls";
    $mail->Port = 587;

    $mail->setFrom('ribushvil@gmail.com', 'K2 Band Rehearsal Studios');
    $mail->addAddress($email);
    $mail->Subject = "Verification Link";
    
    $mail->isHTML(true); // Add this line

    $email_template = "
        <h2>You have registered with K2 Band Rehearsal Studios!</h2>
        <h5>Verify your email address to login with the below given link</h5>
        <br>
        <a href='http://localhost/PRAJ/SIAA/Registration/verify-email.php?token=$token_key'>Click here to verify!</a>
    ";

    $mail->Body = $email_template;
    $mail->send();
}

if (isset($_POST['reg-button'])) {
    $fname = $_POST["fname"];
    $lname = $_POST["lname"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $email = $_POST["email"];
    $token_key = md5(rand());

    // Check if the email is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['status'] = "Email ID is not valid.";
        header("Location: Registration.php");
        exit(0); // Prevent further execution
    }

    // Check if the password is valid
    if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password)) {
        $_SESSION['status'] = "Password must be at least 8 characters long and contain at least one capital letter.";
        header("Location: Registration.php");
        exit(0); // Prevent further execution
    }

    // Check for existing username
    $check_username_query = "SELECT username FROM users WHERE username='$username' LIMIT 1";
    $check_username_query_run = mysqli_query($con, $check_username_query);

    if (mysqli_num_rows($check_username_query_run) > 0) {
        $_SESSION['status'] = "Username already exists.";
        header("Location: Registration.php");
        exit(0); // Prevent further execution
    }

    // Check for existing email
    $check_email_query = "SELECT email FROM users WHERE email='$email' LIMIT 1";
    $check_email_query_run = mysqli_query($con, $check_email_query);

    if (mysqli_num_rows($check_email_query_run) > 0) {
        $_SESSION['status'] = "Email ID already exists.";
        header("Location: Registration.php");
        exit(0); // Prevent further execution
    } else {
        // Register User
        $query = "INSERT INTO users (username, password, first_name, last_name, email, token_key, role) VALUES ('$username', '$password', '$fname', '$lname', '$email', '$token_key', 'user')";
        $query_run = mysqli_query($con, $query);

        if ($query_run) {
            sendemail_verify("$fname", "$email", "$token_key");
            $_SESSION['status'] = "Registration Success! Please verify your email address.";
            header("Location: Registration.php");
            exit(0); // Prevent further execution
        } else {
            $_SESSION['status'] = "Registration Failed.";
            header("Location: Registration.php");
            exit(0); // Prevent further execution
        }
    }
}
?>
