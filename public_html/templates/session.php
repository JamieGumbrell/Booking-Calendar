<?php
// Initialize the session
session_start();
// Check if the user is logged in otherwise redirect them to the login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>