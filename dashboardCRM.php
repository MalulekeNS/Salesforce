<?php
// Include the database connection file
include_once '../db_config.php';

// Fetch dashboard data (e.g., sales summary, lead summary, task summary)
$query = "SELECT 
            (SELECT COUNT(*) FROM leads) as total_leads, 
            (SELECT COUNT(*) FROM customers) as total_customers, 
            (SELECT COUNT(*) FROM sales) as total_sales, 
            (SELECT COUNT(*) FROM tasks) as total_tasks 
          FROM dual";

$result = mysqli_query($conn, $query);
$dashboardData = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - TekSA CRM</title>
</head>
<body>
    <h2>Dashboard Overview</h2>
    <div>
        <p>Total Leads: <?php echo $dashboardData['total_leads']; ?></p>
        <p>Total Customers: <?php echo $dashboardData['total_customers']; ?></p>
        <p>Total Sales: <?php echo $dashboardData['total_sales']; ?></p>
        <p>Total Tasks: <?php echo $dashboardData['total_tasks']; ?></p>
    </div>
</body>
</html>
