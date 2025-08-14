<?php
$host = 'localhost';
$db   = 'africar0r5w4_afbw';
$user = 'africar0r5w4_admin';
$pass = 'Sevezile@089';

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Enable exceptions for errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,      // Return associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                 // Use real prepared statements
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci" // Enforce charset and collation
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    // For security reasons, avoid exposing DB details in production
    echo json_encode(["error" => "Database connection failed. Please contact the administrator."]);
    // Uncomment the line below for debugging (remove in production)
    // echo json_encode(["error" => "Database connection failed: " . $e->getMessage()]);
    exit;
}
?>
