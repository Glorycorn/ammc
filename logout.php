<?php
// Start the session
session_start();

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to login page or homepage
header("Location: index.php"); // Or 'login.php' if you want to redirect to the login page
exit;
?>
