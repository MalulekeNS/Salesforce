<?php
// Include the database connection file
include_once '../db_config.php';

// Fetch sales data
$query = "SELECT * FROM sales";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales - TekSA CRM</title>
</head>
<body>
    <h2>Sales Management</h2>
    <table border="1">
        <tr>
            <th>Sale ID</th>
            <th>Customer Name</th>
            <th>Product</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo $row['sale_id']; ?></td>
                <td><?php echo $row['customer_name']; ?></td>
                <td><?php echo $row['product']; ?></td>
                <td><?php echo $row['amount']; ?></td>
                <td><?php echo $row['status']; ?></td>
                <td><?php echo $row['sale_date']; ?></td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
