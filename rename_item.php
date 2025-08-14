<?php
session_start();
include 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') die("Unauthorized");

$fileId = $_GET['id'] ?? '';
$newName = $_GET['new_name'] ?? '';
if (!$fileId || !$newName) die("Invalid request");

$stmt = $pdo->prepare("SELECT file_path FROM company_files WHERE id = ?");
$stmt->execute([$fileId]);
$file = $stmt->fetch();

if ($file) {
    $oldPath = $file['file_path'];
    $dir = dirname($oldPath);
    $newPath = "$dir/$newName";

    if (rename($oldPath, $newPath)) {
        $pdo->prepare("UPDATE company_files SET file_path = ? WHERE id = ?")->execute([$newPath, $fileId]);
        echo "Renamed";
    } else {
        echo "Failed to rename";
    }
} else {
    echo "File not found";
}
?>
