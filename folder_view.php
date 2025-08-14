<?php
session_start();
include 'db.php';

$companyName = $_GET['name'] ?? '';
$folderName = $_GET['folder'] ?? '';

if (!$companyName || !$folderName) {
    die("Invalid request.");
}

$isAdmin = ($_SESSION['role'] ?? '') === 'admin';

$stmt = $pdo->prepare("SELECT id FROM companies WHERE name = ?");
$stmt->execute([$companyName]);
$company = $stmt->fetch();

if (!$company) {
    die("Company not found.");
}

$companyId = $company['id'];

$stmt = $pdo->prepare("SELECT * FROM company_files WHERE company_id = ?");
$stmt->execute([$companyId]);
$allFiles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Filter files inside the selected folder
$files = [];
foreach ($allFiles as $f) {
    $path = $f['file_path'];
    $relative = str_replace("uploads/", '', $path);
    $parts = explode('/', $relative);
    $folder = count($parts) > 2 ? $parts[1] : 'Root';
    if ($folder === $folderName) {
        $files[] = $f;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($folderName) ?> - Folder</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: url('wp3519537.webp') no-repeat center center fixed;
      background-size: cover;
      color: white;
      display: flex;
    }

    .sidebar {
      width: 220px;
      background-color: rgba(0, 70, 20, 0.85);
      padding: 20px;
      display: flex;
      flex-direction: column;
      gap: 20px;
      min-height: 100vh;
    }

    .sidebar a {
      background-color: #2e7d32;
      color: white;
      text-decoration: none;
      padding: 12px;
      border-radius: 8px;
      text-align: left;
    }

    .sidebar a:hover {
      background-color: #050f06;
    }

    .main-content {
      flex: 1;
      padding: 20px;
    }

    .top-buttons {
      padding-bottom: 20px;
      display: flex;
      gap: 10px;
    }

    .top-buttons a {
      background-color: #2e7d32;
      color: white;
      text-decoration: none;
      padding: 10px 15px;
      border-radius: 8px;
      font-size: 14px;
    }

    .top-buttons a:hover {
      background-color: #050f06;
    }

    .container {
      background-color: rgba(0, 0, 0, 0.6);
      border-radius: 12px;
      padding: 20px;
    }

    h1 {
      text-align: center;
      margin-bottom: 20px;
    }

    .search-bar {
      text-align: center;
      margin-bottom: 30px;
    }

    .search-bar input {
      width: 60%;
      padding: 10px;
      border-radius: 8px;
      border: none;
      font-size: 16px;
    }

    .file-list {
      list-style: none;
      padding: 0;
    }

    .file-list li {
      background-color: white;
      color: black;
      margin-bottom: 8px;
      padding: 10px;
      border-radius: 6px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .file-list li button {
      background-color: #2e7d32;
      border: none;
      color: white;
      padding: 6px 12px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 12px;
      margin-left: 6px;
    }

    .file-list li button:hover {
      background-color: #050f06;
    }

    .rename-form {
      text-align: center;
      margin-bottom: 20px;
    }

    .rename-form input {
      padding: 8px;
      font-size: 14px;
      border-radius: 6px;
      border: none;
      width: 200px;
    }

    .rename-form button {
      background-color: #2e7d32;
      color: white;
      border: none;
      padding: 8px 14px;
      border-radius: 6px;
      font-size: 14px;
      margin-left: 10px;
    }
  </style>
</head>
<body>

<div class="sidebar">
  <a href="company_dashboard.php?name=<?= urlencode($companyName) ?>"><i class="fas fa-chart-bar"></i> Dashboard</a>
  <a href="company_reports.php?name=<?= urlencode($companyName) ?>"><i class="fas fa-file-alt"></i> Reports</a>
  <a href="data_room_company.php?name=<?= urlencode($companyName) ?>"><i class="fas fa-folder-open"></i> Data Room</a>
</div>

<div class="main-content">
  <div class="top-buttons">
    <a href="javascript:history.back()"><i class="fas fa-arrow-left"></i> Back</a>
    <a href="index.php"><i class="fas fa-home"></i> Home</a>
  </div>

  <div class="container">
    <h1><?= htmlspecialchars($folderName) ?> - Folder</h1>

    <div class="search-bar">
      <input type="text" id="searchInput" onkeyup="filterFiles()" placeholder="Search files in this folder..." />
    </div>

    <?php if ($isAdmin): ?>
      <div class="rename-form">
        <form method="post" action="rename_folder.php">
          <input type="hidden" name="company" value="<?= htmlspecialchars($companyName) ?>">
          <input type="hidden" name="old_folder" value="<?= htmlspecialchars($folderName) ?>">
          <input type="text" name="new_folder" placeholder="New folder name" required />
          <button type="submit">Rename Folder</button>
        </form>
      </div>
    <?php endif; ?>

    <ol class="file-list" id="fileList" start="1">
      <?php foreach ($files as $file): ?>
        <li>
          <?= htmlspecialchars($file['filename']) ?>
          <div>
            <a href="<?= htmlspecialchars($file['file_path']) ?>" target="_blank"><button>View</button></a>
            <a href="<?= htmlspecialchars($file['file_path']) ?>" download><button>Download</button></a>
            <?php if ($isAdmin): ?>
              <form method="post" action="delete_file.php" style="display:inline;" onsubmit="return confirm('Delete this file?');">
                <input type="hidden" name="file_id" value="<?= $file['id'] ?>">
                <input type="hidden" name="company" value="<?= htmlspecialchars($companyName) ?>">
                <button type="submit">Delete</button>
              </form>
            <?php endif; ?>
          </div>
        </li>
      <?php endforeach; ?>
    </ol>
  </div>
</div>

<script>
  function filterFiles() {
    const input = document.getElementById("searchInput").value.toLowerCase();
    const files = document.querySelectorAll("#fileList li");
    let anyVisible = false;

    files.forEach(file => {
      const fileName = file.textContent.toLowerCase();
      const visible = fileName.includes(input);
      file.style.display = visible ? "flex" : "none";
      if (visible) anyVisible = true;
    });

    if (!anyVisible) {
      document.getElementById("fileList").innerHTML = "<p style='text-align:center; color:white;'>No matching files found.</p>";
    }
  }
</script>

</body>
</html>
