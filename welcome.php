<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect to the login page if not logged in
    header("Location: login.html");
    exit;
}

// Display welcome message
echo "Welcome, " . htmlspecialchars($_SESSION['username']) . "!";
?>