<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

include 'db.php';

$stmt = $pdo->query("SELECT * FROM audit_logs ORDER BY created_at DESC");
$logs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Audit Logs</title>
  <style>
    body {
      font-family: Arial;
      background: #f4f4f4;
      padding: 40px;
      color: #333;
    }

    .log-container {
      max-width: 800px;
      margin: auto;
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    h2 {
      margin-top: 0;
      color: #2e7d32;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th, td {
      padding: 10px;
      border-bottom: 1px solid #ddd;
      text-align: left;
    }

    th {
      background-color: #2e7d32;
      color: white;
    }

    tr:nth-child(even) {
      background-color: #f2f2f2;
    }

    .btn {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 20px;
      background: #2e7d32;
      color: white;
      text-decoration: none;
      border-radius: 6px;
    }

    .btn:hover {
      background: #1b5e20;
    }
  </style>
</head>
<body>

  <div class="log-container">
    <h2>Audit Logs (Profile Updates)</h2>

    <table>
      <thead>
        <tr>
          <th>User</th>
          <th>Action</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($logs as $log): ?>
          <tr>
            <td><?= htmlspecialchars($log['username']) ?></td>
            <td><?= htmlspecialchars($log['action']) ?></td>
            <td><?= date('F j, Y H:i', strtotime($log['created_at'])) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <a class="btn" href="admin_panel.php">← Back to Admin Panel</a>
  </div>

</body>
</html>
