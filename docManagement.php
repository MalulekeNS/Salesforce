<?php
// Include the database connection file
include_once '../db_config.php';

// Fetch document data
$query = "SELECT * FROM documents";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Management - TekSA CRM</title>
</head>
<body>
    <h2>Document Management</h2>
    <table border="1">
        <tr>
            <th>Document ID</th>
            <th>Document Name</th>
            <th>Uploaded By</th>
            <th>Date Uploaded</th>
            <th>Download</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo $row['document_id']; ?></td>
                <td><?php echo $row['document_name']; ?></td>
                <td><?php echo $row['uploaded_by']; ?></td>
                <td><?php echo $row['upload_date']; ?></td>
                <td><a href="uploads/<?php echo $row['file_path']; ?>" download>Download</a></td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
