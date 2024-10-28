<?php
// Include the database connection file
include_once '../db_config.php';

// Fetch lead data
$query = "SELECT * FROM leads";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lead Tracking - TekSA CRM</title>
</head>
<body>
    <h2>Lead Tracking</h2>
    <table border="1">
        <tr>
            <th>Lead ID</th>
            <th>Lead Name</th>
            <th>Email</th>
            <th>Status</th>
            <th>Date Created</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo $row['lead_id']; ?></td>
                <td><?php echo $row['lead_name']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['status']; ?></td>
                <td><?php echo $row['date_created']; ?></td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
