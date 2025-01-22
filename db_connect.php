<?php
// Database connection settings
$host = "localhost";
$user = "root"; // Change as needed
$pass = ""; // Change as needed
$dbname = "crm_database";

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
