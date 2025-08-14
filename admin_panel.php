<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

include 'db.php';

// Approve users
if (isset($_GET['approve']) && is_numeric($_GET['approve'])) {
    $pdo->prepare("UPDATE users SET approved = 1 WHERE id = ?")->execute([$_GET['approve']]);
    header("Location: admin_panel.php");
    exit;
}

// Rename companies
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rename_company'])) {
    $pdo->prepare("UPDATE companies SET name = ? WHERE id = ?")->execute([$_POST['new_name'], $_POST['company_id']]);
    header("Location: admin_panel.php");
    exit;
}

// Get data
$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$companies = $pdo->query("SELECT c.*, COALESCE(f.total, 0) AS file_count FROM companies c LEFT JOIN (SELECT company_id, COUNT(*) as total FROM company_files GROUP BY company_id) f ON c.id = f.company_id ORDER BY file_count DESC")->fetchAll(PDO::FETCH_ASSOC);
$events = $pdo->query("SELECT * FROM programme_events ORDER BY event_date DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      background: url('wp3519537.webp') no-repeat center center fixed;
      background-size: cover;
      color: white;
      display: flex;
      height: 100vh;
    }
    .sidebar {
      width: 220px;
      background-color: rgba(0, 70, 20, 0.9);
      padding: 20px;
      color: white;
      height: 100vh;
      position: fixed;
      left: 0;
      top: 0;
      display: flex;
      flex-direction: column;
      gap: 20px;
    }
    .sidebar a, .sidebar button {
      background-color: #2e7d32;
      color: white;
      padding: 12px;
      border-radius: 8px;
      text-align: left;
      cursor: pointer;
      text-decoration: none;
    }
    .sidebar a:hover, .sidebar button:hover {
      background-color: #050f06;
    }
    .content {
      margin-left: 240px;
      padding: 30px;
      flex: 1;
      background-color: rgba(0, 0, 0, 0.6);
      color: white;
      height: 100%;
      overflow-y: auto;
    }
    h1 {
      color: #d4af37;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background: rgba(34, 51, 34, 0.3);
      backdrop-filter: blur(10px);
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.2);
      margin-bottom: 40px;
    }
    th, td {
      padding: 12px;
      border: 1px solid rgba(255,255,255,0.1);
      color: white;
    }
    th {
      background-color: rgba(46, 125, 50, 0.85);
    }
    tr:nth-child(even) {
      background-color: rgba(255, 255, 255, 0.05);
    }
    .btn {
      background-color: #2e7d32;
      color: white;
      padding: 8px 16px;
      border-radius: 8px;
      border: none;
      cursor: pointer;
    }
    .btn.red {
      background: #c0392b;
    }
    .btn:hover {
      background-color: #1b5e20;
    }
    .btn.red:hover {
      background: #922b21;
    }
    .top-buttons {
      margin-bottom: 30px;
      display: flex;
      gap: 15px;
    }
    .top-buttons a {
      background-color: #2e7d32;
      color: white;
      padding: 10px 15px;
      border-radius: 8px;
      text-decoration: none;
      font-size: 14px;
    }
    .top-buttons a:hover {
      background-color: #050f06;
    }
    input[type="text"], input[type="datetime-local"], textarea {
      padding: 8px;
      border-radius: 6px;
      border: none;
      background-color: rgba(255,255,255,0.15);
      color: white;
    }
    .highlight {
      background-color: rgba(255, 249, 196, 0.8) !important;
      color: black;
    }
    .upload-zone {
      background: rgba(255,255,255,0.1);
      padding: 20px;
      border: 2px dashed #2e7d32;
      margin: 10px 0 20px;
      border-radius: 10px;
      text-align: center;
      cursor: pointer;
    }
  </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <a href="upload_report_data.php"><i class="fas fa-file-upload"></i> Upload Report CSV</a>
  <a href="audit_logs.php"><i class="fas fa-clipboard-list"></i> View Audit Logs</a>
  <a href="templates/index.php" class="btn"><i class="fas fa-file-alt"></i> Templates</a> <!-- Link to Templates -->
  <div class="upload-zone" onclick="document.getElementById('fileUpload').click();">
    <i class="fas fa-file"></i> Upload Files<br>(drag or click)
  </div>
  <input type="file" id="fileUpload" multiple style="display:none;">
  <div class="upload-zone" onclick="document.getElementById('folderUpload').click();">
    <i class="fas fa-folder-open"></i> Upload Folder<br>(drag or click)
  </div>
  <input type="file" id="folderUpload" webkitdirectory directory style="display:none;">
</div>

<!-- Content -->
<div class="content">
  <div class="top-buttons">
    <a href="javascript:history.back()"><i class="fas fa-arrow-left"></i> Back</a>
    <a href="index.php"><i class="fas fa-home"></i> Home</a>
  </div>
  <h1>Admin Panel</h1>

  <h2>User Management</h2>
  <table>
    <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Approved</th><th>Action</th></tr>
    <?php foreach ($users as $u): ?>
    <tr>
      <td><?= $u['id'] ?></td>
      <td><?= htmlspecialchars($u['name']) ?></td>
      <td><?= htmlspecialchars($u['email']) ?></td>
      <td><?= $u['role'] ?></td>
      <td><?= $u['approved'] ? 'Yes' : 'No' ?></td>
      <td><?php if (!$u['approved']): ?><a class="btn" href="?approve=<?= $u['id'] ?>">Approve</a><?php else: ?>-<?php endif; ?></td>
    </tr>
    <?php endforeach; ?>
  </table>

  <h2>Company Management</h2>
  <form method="post" action="create_company.php" style="margin-bottom: 20px;">
    <input type="text" name="company_name" placeholder="New Company Name" required />
    <button class="btn" type="submit">Add Company</button>
  </form>

  <table>
    <tr><th>ID</th><th>Name (Files)</th><th>Actions</th></tr>
    <?php foreach ($companies as $c): ?>
    <tr<?= $c['file_count'] == 0 ? ' class="highlight"' : '' ?>>
      <td><?= $c['id'] ?></td>
      <td>
        <?= htmlspecialchars($c['name']) ?> (<?= $c['file_count'] ?> files)
        <form method="post" action="admin_panel.php" style="display:inline;">
          <input type="hidden" name="company_id" value="<?= $c['id'] ?>" />
          <input type="text" name="new_name" placeholder="New name" required />
          <button class="btn" type="submit" name="rename_company">Rename</button>
        </form>
      </td>
      <td>
        <a class="btn" href="data_room_company.php?name=<?= urlencode($c['name']) ?>">Data Room</a>
        <form method="post" action="delete_company.php" onsubmit="return confirm('Delete this company?');" style="display:inline;">
          <input type="hidden" name="company_id" value="<?= $c['id'] ?>" />
          <button class="btn red" type="submit">Delete</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>

  <h2>Calendar Events</h2>
  <a class="btn" href="add_event.php" style="margin-bottom: 20px;">Add Event</a>
  <table>
    <tr><th>ID</th><th>Title</th><th>Date</th><th>Description</th><th>Actions</th></tr>
    <?php foreach ($events as $event): ?>
    <tr>
      <td><?= $event['id'] ?></td>
      <td><?= htmlspecialchars($event['title']) ?></td>
      <td><?= date('j M Y H:i', strtotime($event['event_date'])) ?></td>
      <td><?= htmlspecialchars($event['description']) ?></td>
      <td>
        <form method="post" action="delete_event.php" onsubmit="return confirm('Delete this event?');" style="display:inline;">
          <input type="hidden" name="event_id" value="<?= $event['id'] ?>" />
          <button class="btn red" type="submit">Delete</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>

</body>
</html>
