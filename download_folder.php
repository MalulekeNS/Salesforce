<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

$folder = $_GET['folder'] ?? '';
$baseDir = realpath(__DIR__ . '/uploads');

$targetDir = realpath($baseDir . '/' . $folder);
if (!$targetDir || strpos($targetDir, $baseDir) !== 0) {
    die("Invalid folder.");
}

$zipName = basename($targetDir) . ".zip";
$zipPath = sys_get_temp_dir() . '/' . $zipName;

$zip = new ZipArchive();
if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($targetDir),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $file) {
        if (!$file->isDir()) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($targetDir) + 1);
            $zip->addFile($filePath, $relativePath);
        }
    }
    $zip->close();

    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . $zipName . '"');
    header('Content-Length: ' . filesize($zipPath));
    readfile($zipPath);
    unlink($zipPath);
    exit;
} else {
    die("Failed to create ZIP.");
}
