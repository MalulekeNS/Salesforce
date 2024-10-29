<?php
// Include the database connection file
include_once '../db_config.php';

// Fetch report data
$query = "SELECT * FROM reports";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics - TekSA CRM</title>
</head>
<body>
    <h2>Reports & Analytics</h2>
    <table border="1">
        <tr>
            <th>Report ID</th>
            <th>Report Name</th>
            <th>Date Generated</th>
            <th>Download</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo $row['report_id']; ?></td>
                <td><?php echo $row['report_name']; ?></td>
                <td><?php echo $row['date_generated']; ?></td>
                <td><a href="downloads/<?php echo $row['file_path']; ?>" download>Download</a></td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
