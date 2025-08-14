<?php
session_start();
include 'db.php';

if ($_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

$folder = $_GET['folder'] ?? '';
$companyId = $_GET['company_id'] ?? '';

$baseDir = realpath(__DIR__ . '/uploads');
$targetDir = realpath($baseDir . '/' . $folder);

// Prevent path traversal
if (!$targetDir || strpos($targetDir, $baseDir) !== 0) {
    die("Invalid folder path.");
}

// Delete files from DB that match this folder path
$stmt = $pdo->prepare("DELETE FROM company_files WHERE company_id = ? AND file_path LIKE ?");
$stmt->execute([$companyId, "$targetDir%"]);

// Recursively delete folder
function deleteFolder($dir) {
    if (!is_dir($dir)) return false;
    $items = array_diff(scandir($dir), ['.', '..']);
    foreach ($items as $item) {
        $path = "$dir/$item";
        is_dir($path) ? deleteFolder($path) : unlink($path);
    }
    return rmdir($dir);
}

if (deleteFolder($targetDir)) {
    echo "Folder and records deleted.";
} else {
    echo "Failed to delete folder.";
}
?>
