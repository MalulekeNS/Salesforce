<?php
session_start();
include 'db.php';

if (!in_array($_SESSION['role'] ?? '', ['admin', 'manager'])) {
    die("Access denied.");
}

$popupMessage = '';
$showPopup = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $companyId = $_POST['company_id'] ?? '';
    $folderName = trim($_POST['folder_name'] ?? '');
    $uploadedBy = $_SESSION['user_id'] ?? 0;

    $stmt = $pdo->prepare("SELECT name FROM companies WHERE id = ?");
    $stmt->execute([$companyId]);
    $company = $stmt->fetchColumn();

    if (!$company) {
        $popupMessage = "Invalid company.";
        $showPopup = true;
    } else {
        $targetDir = "uploads/" . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $company);
        if ($folderName !== '') {
            $targetDir .= "/" . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $folderName);
        }

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $uploaded = 0;
        foreach ($_FILES['files']['tmp_name'] as $key => $tmpName) {
            if ($_FILES['files']['error'][$key] !== UPLOAD_ERR_OK) continue;

            $originalName = basename($_FILES['files']['name'][$key]);
            $safeName = time() . "_" . preg_replace('/[^a-zA-Z0-9_\.\-]/', '_', $originalName);
            $destination = "$targetDir/$safeName";

            if (move_uploaded_file($tmpName, $destination)) {
                $stmt = $pdo->prepare("INSERT INTO company_files (company_id, filename, file_path, uploaded_by, uploaded_at) VALUES (?, ?, ?, ?, NOW())");
                $stmt->execute([$companyId, $originalName, $destination, $uploadedBy]);
                $uploaded++;
            }
        }

        $popupMessage = $uploaded > 0
            ? "$uploaded file(s) uploaded successfully."
            : "No files were uploaded.";
        $showPopup = true;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Upload Files</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    body {
      font-family: Arial, sans-serif;
      background: url('des.png') no-repeat center center;
      background-size: cover;
      color: white;
      min-height: 100vh;
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .container {
      background-color: rgba(0,0,0,0.6);
      padding: 30px;
      border-radius: 12px;
      width: 400px;
    }

    h2 {
      text-align: center;
      color: #d4af37;
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin-bottom: 6px;
      font-weight: bold;
    }

    input, select {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: none;
      border-radius: 6px;
      background-color: #114216;
      color: white;
    }

    input[type="file"] {
      background: transparent;
    }

    button {
      width: 100%;
      background-color: #06101f;
      border: none;
      color: white;
      padding: 10px;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
    }

    button:hover {
      background-color: #0ea603;
    }

    .top-buttons {
      position: fixed;
      top: 20px;
      left: 20px;
      display: flex;
      gap: 15px;
    }

    .top-buttons a {
      background-color: #025373;
      color: white;
      padding: 10px 15px;
      border-radius: 8px;
      text-decoration: none;
      font-size: 14px;
    }

    .top-buttons a:hover {
      background-color: #62BF04;
    }

    .popup-overlay {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.6);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 999;
    }

    .popup-box {
      background: white;
      color: black;
      border-radius: 12px;
      padding: 30px 40px;
      width: 300px;
      text-align: center;
      box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }

    .popup-box button {
      background-color: #2e7d32;
      border: none;
      color: white;
      padding: 10px 20px;
      margin-top: 20px;
      border-radius: 8px;
      cursor: pointer;
    }

    .popup-box button:hover {
      background-color: #1b5e20;
    }
  </style>
</head>
<body>

<?php if ($showPopup): ?>
  <div class="popup-overlay">
    <div class="popup-box">
      <p><?= htmlspecialchars($popupMessage) ?></p>
      <form method="get" action="data_room_company.php">
        <input type="hidden" name="name" value="<?= htmlspecialchars($company ?? '') ?>">
        <button type="submit">OK</button>
      </form>
    </div>
  </div>
<?php endif; ?>

<div class="top-buttons">
  <a href="javascript:history.back()"><i class="fas fa-arrow-left"></i> Back</a>
  <a href="index.php"><i class="fas fa-home"></i> Home</a>
</div>

<div class="container">
  <h2>Upload Files to Company</h2>
  <form method="POST" enctype="multipart/form-data">
    <label>Select Company</label>
    <select name="company_id" required>
      <option value="">-- Select Company --</option>
      <?php
        $companies = $pdo->query("SELECT id, name FROM companies ORDER BY name ASC")->fetchAll();
        foreach ($companies as $c) {
          echo "<option value='{$c['id']}'>" . htmlspecialchars($c['name']) . "</option>";
        }
      ?>
    </select>

    <label>Optional Folder Name</label>
    <input type="text" name="folder_name" placeholder="e.g. Reports2025" />

    <label>Select Files (All Formats Allowed)</label>
    <input type="file" name="files[]" multiple required />

    <button type="submit">Upload</button>
  </form>
</div>

</body>
</html>
