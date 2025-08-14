<?php
session_start();

// Ensure the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

include 'db.php';

// Handle renaming of companies
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rename_company'])) {
    $companyId = $_POST['company_id'];
    $newName = $_POST['new_name'];

    // Validate input
    if (empty($companyId) || empty($newName)) {
        die("Missing input.");
    }

    // Sanitize the new company name
    $newName = filter_var($newName, FILTER_SANITIZE_STRING);

    // Fetch the old company name from the database
    $stmt = $pdo->prepare("SELECT name FROM companies WHERE id = :id");
    $stmt->execute(['id' => $companyId]);
    $old = $stmt->fetch();

    if (!$old) {
        die("Company not found.");
    }

    $oldName = $old['name'];

    // If the new name is the same as the old one, don't proceed
    if (strtolower($newName) === strtolower($oldName)) {
        die("The new name must be different from the old name.");
    }

    // Update the company name in the database
    $stmt = $pdo->prepare("UPDATE companies SET name = :name WHERE id = :id");
    $stmt->execute(['name' => $newName, 'id' => $companyId]);

    // Redirect back to the company list to reflect changes
    header("Location: data_room_companies.php");
    exit;
}

// Fetch companies from the database
$companies = $pdo->query("SELECT * FROM companies ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Data Room - Companies</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    /* Your existing CSS code here */
  </style>
</head>
<body>

  <!-- Back & Home Buttons -->
  <div class="top-buttons">
    <button onclick="history.back()"><i class="fas fa-arrow-left"></i> Back</button>
    <a href="index.php"><i class="fas fa-home"></i> Home</a>
  </div>

  <!-- Main Grid -->
  <div class="overlay">
    <h1>Select a Company to View Data Room</h1>

    <div class="grid-container">
      <?php foreach ($companies as $company): ?>
        <div class="company-card">
          <a href="data_room_company.php?name=<?= urlencode($company['name']) ?>">
            <?= htmlspecialchars($company['name']) ?>
          </a>

          <!-- Rename Company Form -->
          <form method="POST" class="rename-form">
            <input type="hidden" name="company_id" value="<?= $company['id'] ?>" />
            <input type="text" name="new_name" placeholder="New Name" required />
            <button type="submit" name="rename_company">Rename</button>
          </form>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

</body>
</html>
