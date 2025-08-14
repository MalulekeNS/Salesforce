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
    $newDescription = $_POST['description'] ?? '';
    $stmt = $pdo->prepare("UPDATE folders SET description = ? WHERE company_name = ? AND folder_name = ?");
    $stmt->execute([$newDescription, $company, $folder]);
    echo "<p style='color:green;'>Metadata updated successfully.</p>";
    $currentDescription = $newDescription;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Folder - <?= htmlspecialchars($folder) ?></title>
</head>
<body>
    <h1>Edit Folder: <?= htmlspecialchars($folder) ?> (<?= htmlspecialchars($company) ?>)</h1>
    <form method="post">
        <label for="description">Folder Description:</label><br>
        <textarea name="description" id="description" rows="4" cols="50"><?= htmlspecialchars($currentDescription) ?></textarea><br><br>
        <input type="submit" value="Save">
    </form>
</body>
</html>
