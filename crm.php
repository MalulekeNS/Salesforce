<?php
session_start();

// Include database connection file
include_once 'db_config.php';

// Handle routing for different pages
if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 'dashboard'; // Default to dashboard
}

// Function to load the respective page
function loadPage($page) {
    switch ($page) {
        case 'dashboard':
            include 'pages/dashboard.php';
            break;
        case 'lead_tracking':
            include 'pages/lead_tracking.php';
            break;
        case 'customerManagement':
            include 'pages/customerManagement.php';
            break;
        case 'sales':
            include 'pages/sales.php';
            break;
        case 'taskManagement':
            include 'pages/taskManagement.php';
            break;
        case 'documentManagement':
            include 'pages/documentManagement.php';
            break;
        case 'reportsAnalytics':
            include 'pages/reportsAnalytics.php';
            break;
        default:
            include 'pages/dashboard.php'; // Default to dashboard if no valid page is found
            break;
    }
}

// Call the function to load the required page
loadPage($page);

// Database connection (db_config.php)
function dbConnect() {
    $host = 'localhost'; // Change as per your settings
    $user = 'root';      // Database user
    $pass = '';          // Database password
    $dbname = 'crm_database'; // Database name

    $conn = new mysqli($host, $user, $pass, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

// Function to fetch dashboard data
function getDashboardData() {
    $conn = dbConnect();
    $sql = "SELECT COUNT(*) as total_sales, 
                   (SELECT COUNT(*) FROM leads WHERE status='new') as new_leads, 
                   (SELECT COUNT(*) FROM tasks WHERE status='pending') as pending_tasks, 
                   (SELECT COUNT(*) FROM customers WHERE active=1) as active_customers 
            FROM sales";
    $result = $conn->query($sql);
    $conn->close();

    return $result->fetch_assoc();
}

// Function to fetch data for specific pages (modify as per each page's needs)
// Example for Sales:
function getSalesData() {
    $conn = dbConnect();
    $sql = "SELECT * FROM sales";
    $result = $conn->query($sql);
    $sales_data = [];
    while ($row = $result->fetch_assoc()) {
        $sales_data[] = $row;
    }
    $conn->close();
    return $sales_data;
}
?>
