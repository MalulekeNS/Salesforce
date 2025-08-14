<?php
session_start();
include 'db.php';

$popupMessage = '';
$showPopup = false;

$uploadsBase = $_SERVER['DOCUMENT_ROOT'] . "/uploads/";
$companies = is_dir($uploadsBase) ? array_filter(scandir($uploadsBase), fn($dir) => $dir !== '.' && $dir !== '..' && is_dir($uploadsBase . $dir)) : [];

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $popupMessage = "Access denied.";
    $showPopup = true;
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company = trim($_POST['company'] ?? '');
    $oldFolder = trim($_POST['old_folder'] ?? '');
    $newFolder = trim($_POST['new_folder'] ?? '');

    if (!$company || !$oldFolder || !$newFolder) {
        $popupMessage = "All fields are required.";
        $showPopup = true;
    } else {
        $safeCompany = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $company);
        $safeOld = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $oldFolder);
        $safeNew = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $newFolder);

        if (strpos($safeOld, '..') !== false || strpos($safeNew, '..') !== false) {
            $popupMessage = "Invalid folder name.";
        } else {
            $basePath = $uploadsBase . $safeCompany . '/';
            $oldPath = $basePath . $safeOld;
            $newPath = $basePath . $safeNew;

            if (!is_dir($basePath)) {
                mkdir($basePath, 0755, true);
            }

            if ($safeOld === $safeNew) {
                $popupMessage = "The new folder name is the same as the current name.";
            } elseif (!is_dir($oldPath)) {
                $popupMessage = "Original folder not found.";
            } elseif (is_dir($newPath)) {
                $popupMessage = "A folder with the new name already exists.";
            } else {
                $renamed = rename($oldPath, $newPath);
                if ($renamed) {
                    $popupMessage = "Folder renamed successfully.";
                    $logEntry = date('Y-m-d H:i:s') . " - Folder renamed from '$safeOld' to '$safeNew' by " . ($_SESSION['username'] ?? 'Unknown') . PHP_EOL;
                    file_put_contents(__DIR__ . '/rename_log.txt', $logEntry, FILE_APPEND);
                } else {
                    $popupMessage = "Folder rename failed. Check permissions.";
                }
            }
        }
        $showPopup = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rename Folder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('wp3519537.webp') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }
        form {
            background: rgba(255, 255, 255, 0.9);
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            text-align: center;
        }
        select, input[type="text"] {
            padding: 10px;
            width: 80%;
            margin: 10px 0;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #2e7d32;
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background-color: #1b5e20;
        }
        .popup-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 999;
        }
        .popup-box {
            background: white;
            color: black;
            border-radius: 12px;
            padding: 30px 40px;
            width: 300px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
    </style>
    <script>
        function updateFolders() {
            const company = document.getElementById('company').value;
            const folderSelect = document.getElementById('old_folder');
            folderSelect.innerHTML = '<option value="">Loading...</option>';

            fetch('list_folders.php?company=' + encodeURIComponent(company) + '&_=' + new Date().getTime())
                .then(response => response.json())
                .then(data => {
                    console.log('Updated Folders:', data.folders); // Debugging
                    folderSelect.innerHTML = '<option value="">Select Folder</option>';
                    data.folders.forEach(folder => {
                        const option = document.createElement('option');
                        option.value = folder;
                        option.textContent = folder;
                        folderSelect.appendChild(option);
                    });
                });
        }

        // Auto-refresh folder list after renaming
        function refreshAfterRename() {
            const company = document.getElementById('company').value;
            if (company) {
                updateFolders();
            }
        }
    </script>
</head>
<body>

<?php if ($showPopup): ?>
<div class="popup-overlay">
    <div class="popup-box">
        <p><?= htmlspecialchars($popupMessage) ?></p>
        <button onclick="refreshAfterRename()">OK</button> <!-- Dynamically update the list instead of page reload -->
    </div>
</div>
<?php endif; ?>

<form method="POST">
    <h2>Rename Folder</h2>
    <select name="company" id="company" required onchange="updateFolders()">
        <option value="">Select Company</option>
        <?php foreach ($companies as $comp): ?>
            <option value="<?= htmlspecialchars($comp) ?>"><?= htmlspecialchars($comp) ?></option>
        <?php endforeach; ?>
    </select>

    <select name="old_folder" id="old_folder" required>
        <option value="">Select Folder</option>
    </select>

    <input type="text" name="new_folder" placeholder="New Folder Name" required />
    <button type="submit">Rename</button>
</form>

</body>
</html>
