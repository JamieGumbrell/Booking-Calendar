<?php
session_start();
 
//unset all session variables
$_SESSION = array();
 
//destroy session.
session_destroy();
 
//redirect to home page
header("location: /");
exit;
?>