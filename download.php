<?php
session_start();
include 'db.php';

$company = $_GET['company'] ?? '';
$folder = $_GET['folder'] ?? '';

if (!$company || !$folder) {
    die("Missing parameters.");
}

// Fetch current folder metadata
$stmt = $pdo->prepare("SELECT description FROM folders WHERE company_name = ? AND folder_name = ?");
$stmt->execute([$company, $folder]);
$folderData = $stmt->fetch();

$currentDescription = $folderData['description'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newFolderName = $_POST['new_folder_name'] ?? $folder;
    $newDescription = $_POST['description'] ?? '';

    // Define the paths
    $baseDir = '/path/to/your/folders'; // Replace with your actual base directory
    $oldPath = $baseDir . '/' . $company . '/' . $folder;
    $newPath = $baseDir . '/' . $company . '/' . $newFolderName;

    // Rename the folder in the filesystem
    if ($folder !== $newFolderName) {
        if (file_exists($oldPath)) {
            if (!file_exists($newPath)) {
                if (rename($oldPath, $newPath)) {
                    // Update the folder name in the database
                    $stmt = $pdo->prepare("UPDATE folders SET folder_name = ?, description = ? WHERE company_name = ? AND folder_name = ?");
                    $stmt->execute([$newFolderName, $newDescription, $company, $folder]);
                    echo "<p style='color:green;'>Folder renamed and metadata updated successfully.</p>";
                    $folder = $newFolderName;
                    $currentDescription = $newDescription;
                } else {
                    echo "<p style='color:red;'>Failed to rename the folder in the filesystem.</p>";
                }
            } else {
                echo "<p style='color:red;'>A folder with the new name already exists.</p>";
            }
        } else {
            echo "<p style='color:red;'>The original folder does not exist.</p>";
        }
    } else {
        // Only update the description
        $stmt = $pdo->prepare("UPDATE folders SET description = ? WHERE company_name = ? AND folder_name = ?");
        $stmt->execute([$newDescription, $company, $folder]);
        echo "<p style='color:green;'>Metadata updated successfully.</p>";
        $currentDescription = $newDescription;
    }
}
?>
