<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['folders' => []]);
    exit;
}

$company = trim($_GET['company'] ?? '');
$safeCompany = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $company);
$uploadsBase = $_SERVER['DOCUMENT_ROOT'] . "/uploads/$safeCompany/";

$folders = [];
if (is_dir($uploadsBase)) {
    $folders = array_filter(scandir($uploadsBase), fn($dir) => $dir !== '.' && $dir !== '..' && is_dir($uploadsBase . $dir));
}

echo json_encode(['folders' => array_values($folders)]);
