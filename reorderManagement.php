<?php
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "your_database_name";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Get form data
$productName = $_POST['productName'];
$quantity = intval($_POST['quantity']);
$supplier = $_POST['supplier'];

// Insert purchase order into the database
$query = "INSERT INTO purchase_orders (product_name, quantity, supplier) VALUES ('$productName', $quantity, '$supplier')";
if ($conn->query($query) === TRUE) {
    echo json_encode(["message" => "Purchase order created successfully"]);
} else {
    echo json_encode(["error" => "Error: " . $conn->error]);
}

$conn->close();
?>
