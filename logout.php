<?php
// Start the session
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to logout.html with a success status
header("Location: logout.html?status=success");
exit;
?>