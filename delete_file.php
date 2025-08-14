<?php
session_start();
include 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') die("Unauthorized");

$fileId = $_GET['id'] ?? '';
if (!$fileId) die("Invalid request");

$stmt = $pdo->prepare("SELECT file_path FROM company_files WHERE id = ?");
$stmt->execute([$fileId]);
$file = $stmt->fetch();

if ($file) {
    $filePath = $file['file_path'];
    if (file_exists($filePath)) unlink($filePath);
    $pdo->prepare("DELETE FROM company_files WHERE id = ?")->execute([$fileId]);
    echo "Deleted";
} else {
    echo "File not found";
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Delete</title>
  <style>
    body {
      font-family: Arial;
      background: url('des.png') no-repeat center center fixed;
      background-size: cover;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      color: white;
      margin: 0;
    }
    .popup {
      background: rgba(0, 70, 20, 0.85);
      padding: 30px;
      border-radius: 12px;
      text-align: center;
      box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    }
    button {
      margin-top: 20px;
      background-color: #2e7d32;
      border: none;
      color: white;
      padding: 10px 20px;
      border-radius: 8px;
      cursor: pointer;
    }
    button:hover {
      background-color: #164018;
    }
  </style>
</head>
<body>
  <div class="popup">
    <h3><?= htmlspecialchars($popupMessage) ?></h3>
    <form method="get" action="data_room_company.php">
      <input type="hidden" name="name" value="<?= htmlspecialchars($company_id) ?>">
      <button type="submit">OK</button>
    </form>
  </div>
</body>
</html>
