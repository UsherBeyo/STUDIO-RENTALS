<?php
session_start();
include 'dbcon.php'; // This file contains the database connection

if (isset($_POST['login-btn'])) 
{
    if(!empty(trim($_POST['username'])) && !empty(trim($_POST['password'])))
    {
        $username = mysqli_real_escape_string($con, $_POST['username']);
        $password = mysqli_real_escape_string($con, $_POST['password']);

        $login_query = "SELECT * FROM users WHERE username='$username' AND password='$password' LIMIT 1";
        $login_query_run = mysqli_query($con, $login_query);

        if(mysqli_num_rows($login_query_run) > 0)
        {
            $row = mysqli_fetch_array($login_query_run);
            if($row['verified'] == "1")
            {
                $_SESSION['authenticated'] = TRUE;
                $_SESSION['auth_user'] = [
                    'id' => $row['id'],
                    'username' => $row['username'],
                    'first_name' => $row['first_name'],
                    'last_name' => $row['last_name'],
                    'password' => $row['password'],
                    'email' => $row['email'],
                    'role' => $row['role'],
                ];
                $_SESSION['status'] = "You have logged in successfully!";
                
                // Check user role and redirect accordingly
                if ($row['role'] == 'admin') {
                    header("Location: Admin/index.php");
                } elseif ($row['role'] == 'staff') {
                    header("Location: Staff/staff.php");
                } else {
                    header("Location: index.php");
                }
                exit(0);
            }
            else
            {
                $_SESSION['status'] = "We've sent you a verification form in your email. Please verify your email address to login.";
                header("Location: index.php");
                exit(0);
            }
        }
        else
        {
            $_SESSION['status'] = "Invalid Email or Password, Please try again.";
            header("Location: index.php");
            exit(0);
        }
    }
    else
    {
        $_SESSION['status'] = "All fields are mandatory, please login again.";
        header("Location: index.php");
        exit(0);
    }
}
?>
