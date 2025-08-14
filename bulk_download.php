<?php
session_start();
include 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') die("Unauthorized");

$files = explode(',', $_GET['files'] ?? '');
if (empty($files)) die("No files selected");

$zip = new ZipArchive();
$zipName = "bulk_download_" . time() . ".zip";

if ($zip->open($zipName, ZipArchive::CREATE) !== TRUE) {
    die("Could not create ZIP file");
}

foreach ($files as $id) {
    $stmt = $pdo->prepare("SELECT file_path FROM company_files WHERE id = ?");
    $stmt->execute([$id]);
    $file = $stmt->fetch();

    if ($file && file_exists($file['file_path'])) {
        $zip->addFile($file['file_path'], basename($file['file_path']));
    }
}

$zip->close();

header("Content-Type: application/zip");
header("Content-Disposition: attachment; filename=$zipName");
readfile($zipName);
unlink($zipName); // Clean up after download
exit;
?>
