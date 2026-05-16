<?php
    session_start();
    include('dbcon.php');

    if(isset($_GET['token']))
    {
        $token = $_GET['token'];
        $verify_query = "SELECT token_key, verified FROM users WHERE token_key='$token' LIMIT 1";
        $verify_query_run = mysqli_query($con, $verify_query);

        if(mysqli_num_rows($verify_query_run) > 0)
        {
            $row = mysqli_fetch_array($verify_query_run);

            if($row['verified'] == "0")
            {
                $clicked_token = $row['token_key'];
                $update_query = "UPDATE users SET verified='1' WHERE token_key='$clicked_token' LIMIT 1";
                $update_query_run = mysqli_query($con, $update_query);

                if($update_query_run)
                {
                    $_SESSION['status'] = "Verification Successful! Please Login.";
                    header("Location: ../index.php");
                    exit(0); 
                }
                else
                {
                    $_SESSION['status'] = "Verification failed.";
                    header("Location: ../index.php");
                    exit(0); 
                }
            }
            else
            {
                $_SESSION['status'] = "Email has been verified already, Please login.";
                header("Location: ../index.php");
                exit(0); 
            }
        }
        else
        {
            $_SESSION['status'] = "This verification key does not exists.";
            header("Location: ../index.php");
            exit(0); 
        }
    }
    else
    {
        $_SESSION['status'] = "Not Allowed.";
        header("Location: ../index.php");
        exit(0);
    }

?>