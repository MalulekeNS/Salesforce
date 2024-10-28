<?php
// Include the database connection file
include_once '../db_config.php';

// Fetch task data
$query = "SELECT * FROM tasks";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management - TekSA CRM</title>
</head>
<body>
    <h2>Task Management</h2>
    <table border="1">
        <tr>
            <th>Task ID</th>
            <th>Assigned To</th>
            <th>Description</th>
            <th>Status</th>
            <th>Due Date</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo $row['task_id']; ?></td>
                <td><?php echo $row['assigned_to']; ?></td>
                <td><?php echo $row['description']; ?></td>
                <td><?php echo $row['status']; ?></td>
                <td><?php echo $row['due_date']; ?></td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
