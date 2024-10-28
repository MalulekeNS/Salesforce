<?php
// getReports.php
header('Content-Type: application/json');

// Database connection
include_once '../db_config.php';

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Fetch inventory data
$inventoryQuery = "SELECT product_name, quantity, unit_price FROM inventory";
$inventoryResult = $conn->query($inventoryQuery);

$inventoryData = [];
if ($inventoryResult->num_rows > 0) {
    while($row = $inventoryResult->fetch_assoc()) {
        $inventoryData[] = $row;
    }
}

// Fetch purchase orders data
$purchaseOrdersQuery = "SELECT order_id, supplier_name, order_date, total_amount FROM purchase_orders";
$purchaseOrdersResult = $conn->query($purchaseOrdersQuery);

$purchaseOrdersData = [];
if ($purchaseOrdersResult->num_rows > 0) {
    while($row = $purchaseOrdersResult->fetch_assoc()) {
        $purchaseOrdersData[] = $row;
    }
}

// Return the data as JSON
echo json_encode([
    "inventory" => $inventoryData,
    "purchaseOrders" => $purchaseOrdersData,
]);

$conn->close();
?>
