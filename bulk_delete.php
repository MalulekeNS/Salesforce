<?php
session_start();
include 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') die("Unauthorized");

$files = explode(',', $_GET['files'] ?? '');
if (empty($files)) die("No files selected");

foreach ($files as $id) {
    $stmt = $pdo->prepare("SELECT file_path FROM company_files WHERE id = ?");
    $stmt->execute([$id]);
    $file = $stmt->fetch();

    if ($file && file_exists($file['file_path'])) {
        unlink($file['file_path']);
        $pdo->prepare("DELETE FROM company_files WHERE id = ?")->execute([$id]);
    }
}

echo "Bulk delete completed";
?>
