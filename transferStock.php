<?php
header('Content-Type: application/json');

// Database connection
include_once '../db_config.php';

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Get form data
$fromWarehouse = $_POST['fromWarehouse'];
$toWarehouse = $_POST['toWarehouse'];
$product = $_POST['product'];
$quantity = intval($_POST['quantity']);

// Update stock in fromWarehouse
$updateFromQuery = "UPDATE stock SET quantity = quantity - $quantity WHERE warehouse_id = '$fromWarehouse' AND product_name = '$product'";
$conn->query($updateFromQuery);

// Update stock in toWarehouse
$updateToQuery = "UPDATE stock SET quantity = quantity + $quantity WHERE warehouse_id = '$toWarehouse' AND product_name = '$product'";
$conn->query($updateToQuery);

// Return success message
echo json_encode(["message" => "Stock successfully transferred!"]);

$conn->close();
?>
