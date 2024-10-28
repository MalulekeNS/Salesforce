<?php
include_once '../db_config.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the posted data
    $supplierName = $_POST['supplierName'];
    $contactPerson = $_POST['contactPerson'];
    $phoneNumber = $_POST['phoneNumber'];
    $email = $_POST['email'];
    $barcode = $_POST['barcode'];

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO suppliers (supplierName, contactPerson, phoneNumber, email, barcode) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $supplierName, $contactPerson, $phoneNumber, $email, $barcode);

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(["message" => "Supplier added successfully!"]);
    } else {
        echo json_encode(["message" => "Error adding supplier: " . $stmt->error]);
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>
