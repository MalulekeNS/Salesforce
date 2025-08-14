<?php
session_start();
include 'db.php';

$companyName = $_GET['name'] ?? '';
if (!$companyName) die("No company specified.");

$isAdmin = ($_SESSION['role'] ?? '') === 'admin';

// Fetch Company ID
$stmt = $pdo->prepare("SELECT id FROM companies WHERE name = ?");
$stmt->execute([$companyName]);
$company = $stmt->fetch();
if (!$company) die("Company not found.");
$companyId = $company['id'];

// Fetch Files and Group by Folder
$stmt = $pdo->prepare("SELECT * FROM company_files WHERE company_id = ?");
$stmt->execute([$companyId]);
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);

$folders = [];
foreach ($files as $f) {
    $path = str_replace("uploads/", '', $f['file_path']);
    $parts = explode('/', $path);
    $folder = count($parts) > 2 ? $parts[1] : 'Root';
    $folders[$folder][] = $f;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title><?= htmlspecialchars($companyName) ?> - Data Room</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    background: url('des.png') no-repeat center center fixed;
    background-size: cover;
    color: white;
    display: flex;
    height: 100vh;
}
.sidebar {
    width: 220px;
    background-color: rgba(0, 0, 0, 0.7);
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 15px;
    min-height: 100vh;
}
.sidebar a {
    background-color: #e1f2e3;
    color: black;
    text-decoration: none;
    padding: 15px 25px;
    border-radius: 20px;
    text-align: left;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 15px;
    transition: background-color 0.3s ease;
}
.sidebar a:hover {
    background-color: #62BF04;
}
.main-content {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
}
.top-buttons {
    padding: 20px;
    display: flex;
    gap: 10px;
    background: rgba(0, 0, 0, 0.3);
}
.top-buttons a {
    background-color: #0078D4;
    color: white;
    text-decoration: none;
    padding: 10px 15px;
    border-radius: 8px;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 5px;
}
.top-buttons a:hover {
    background-color: #62BF04;
}
.container {
    margin: 20px;
    background-color: rgba(0, 0, 0, 0.4);
    border-radius: 12px;
    padding: 20px;
    backdrop-filter: blur(20px);
}
h1 {
    text-align: center;
    margin-bottom: 30px;
    color: #FFD700;
}
.category {
    margin-bottom: 30px;
}
.category h2 {
    color: #FFD700;
    margin-bottom: 15px;
}
.file-list {
    list-style: none;
    padding: 0;
}
.file-list li {
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff;
    margin-bottom: 12px;
    padding: 15px;
    border-radius: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}
.file-list li:hover {
    background-color: rgba(255, 255, 255, 0.25);
    transition: background-color 0.3s ease;
}
.file-actions button {
    background-color: #0078D4;
    border: none;
    color: white;
    padding: 8px 14px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 13px;
    margin-left: 5px;
}
.file-actions button:hover {
    background-color: #030736;
}
.bulk-actions {
    text-align: center;
    margin: 20px 0;
}
.bulk-actions button {
    padding: 10px 20px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    background-color: #62BF04;
    color: white;
    margin: 0 10px;
}
.bulk-actions button:hover {
    background-color: #0078D4;
}
</style>
</head>
<body>
<!-- Sidebar Section -->
<div class="sidebar">
  <a href="index.php"><i class="fas fa-home"></i> Home</a>
  <a href="company_dashboard.php?name=<?= urlencode($companyName) ?>"><i class="fas fa-chart-bar"></i> Dashboard</a>
  <a href="company_reports.php?name=<?= urlencode($companyName) ?>"><i class="fas fa-file-alt"></i> Reports</a>
  <a href="data_room_company.php?name=<?= urlencode($companyName) ?>"><i class="fas fa-folder-open"></i> Data Room</a>
  <?php if ($isAdmin): ?>
    <a href="upload_file.php"><i class="fas fa-upload"></i> Upload Files</a>
  <?php endif; ?>
</div>

<!-- Main Content -->
<div class="main-content">
  <div class="top-buttons">
    <a href="data_room_companies.php"><i class="fas fa-arrow-left"></i> Back</a>
    <a href="index.php"><i class="fas fa-home"></i> Home</a>
  </div>

  <div class="container">
    <h1 id="companyTitle"><?= htmlspecialchars($companyName) ?> - Data Room</h1>

    <?php if ($isAdmin): ?>
      <div class="bulk-actions">
        <button onclick="performBulkDownload()">Download Marked</button>
        <button onclick="performBulkDelete()">Delete Marked</button>
      </div>
    <?php endif; ?>

    <?php if (count($folders) > 0): ?>
      <?php foreach ($folders as $folder => $filesInFolder): ?>
        <div class="category">
          <h2>📁 <?= htmlspecialchars($folder) ?></h2>
          <ul class="file-list">
            <?php
            // Sort files by the numerical order in their filename
            usort($filesInFolder, function($a, $b) {
                // Extract numerical parts from the filenames (e.g., "file1.txt" becomes "1")
                preg_match('/(\d+)/', $a['filename'], $matchA);
                preg_match('/(\d+)/', $b['filename'], $matchB);

                // Check if the numerical part was found, otherwise assign a default value (e.g., 0)
                $numA = isset($matchA[0]) ? (int)$matchA[0] : 0;
                $numB = isset($matchB[0]) ? (int)$matchB[0] : 0;

                return $numA - $numB; // Compare the numbers
            });

            // Display sorted files
            foreach ($filesInFolder as $file): 
              $fileName = $file['filename']; // Using 'filename' column here
            ?>
              <li>
                <span>
                  <?php if ($isAdmin): ?>
                    <input type="checkbox" class="file-checkbox" value="<?= htmlspecialchars($file['id']) ?>" />
                  <?php endif; ?>
                  <?= htmlspecialchars($fileName) ?> <!-- Displaying the filename only -->
                </span>
                <div class="file-actions">
                  <a href="uploads/<?= htmlspecialchars($fileName) ?>" target="_blank"><button>View</button></a>
                  <a href="uploads/<?= htmlspecialchars($fileName) ?>" download><button>Download</button></a>
                  <?php if ($isAdmin): ?>
                    <button onclick="deleteFile(<?= $file['id'] ?>)">Delete</button>
                    <button onclick="renameFile(<?= $file['id'] ?>)">Rename</button>
                  <?php endif; ?>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p style="text-align: center;">No folders or files uploaded yet.</p>
    <?php endif; ?>
  </div>
</div>
<script>
// 🗑️ Delete a single file
function deleteFile(fileId) {
  if (confirm('Are you sure you want to delete this file?')) {
    fetch(`delete_file.php?id=${fileId}`)
      .then(response => response.text())
      .then(() => location.reload());
  }
}

// ✏️ Rename a single file
function renameFile(fileId) {
  const newName = prompt('Enter the new file name:');
  if (newName) {
    fetch(`rename_file.php?id=${fileId}&new_name=${encodeURIComponent(newName)}`)
      .then(response => response.text())
      .then(() => location.reload());
  }
}

// 📦 Bulk Download Selected Files
function performBulkDownload() {
  const selected = getSelectedFiles();
  if (selected.length === 0) {
    alert('No files selected.');
    return;
  }
  window.location.href = `bulk_download.php?files=${selected.join(',')}`;
}

// 🗑️ Bulk Delete Selected Files
function performBulkDelete() {
  const selected = getSelectedFiles();
  if (selected.length === 0) {
    alert('No files selected.');
    return;
  }
  if (confirm(`Delete ${selected.length} selected files?`)) {
    fetch(`bulk_delete.php?files=${selected.join(',')}`)
      .then(response => response.text())
      .then(() => location.reload());
  }
}

// ✅ Get Selected Files
function getSelectedFiles() {
  return Array.from(document.querySelectorAll('.file-checkbox:checked')).map(cb => cb.value);
}
</script>
</body>
</html>
