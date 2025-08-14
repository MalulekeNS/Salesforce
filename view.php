<?php
session_start();

// Include the database connection
include $_SERVER['DOCUMENT_ROOT'] . '/db.php'; 

// Check if file ID is provided
if (!isset($_GET['file_id'])) {
    die('File ID is required');
}

$fileId = $_GET['file_id'];

// Fetch the file information from the database
$fileQuery = $pdo->prepare("SELECT * FROM company_files WHERE id = :id");
$fileQuery->execute(['id' => $fileId]);

$file = $fileQuery->fetch();

// If the file doesn't exist, show an error
if (!$file) {
    die("File not found");
}

// Get the file path
$filePath = $_SERVER['DOCUMENT_ROOT'] . '/' . $file['file_path'];

// Check if the file exists
if (!file_exists($filePath)) {
    die("File does not exist on the server");
}

// Display the file (if it's a PDF or an image, it will be shown in the browser)
header('Content-Type: ' . mime_content_type($filePath));
readfile($filePath);
exit;
?>
