<?php
session_start();

if(!isset($_SESSION['authenticated']))
{
    $_SESSION['status'] = "Please Login or Make an Account first to access.";
    header('Location: index.php');
    exit(0);
}
else
{

}


?>