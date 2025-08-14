<?php
session_start();
include 'db.php';

$popupMessage = '';
$showPopup = false;

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $companyName = trim($_POST['company_name'] ?? '');

    if ($companyName === '') {
        $popupMessage = "Company name is required.";
        $showPopup = true;
    } else {
        // Check for duplicates
        $check = $pdo->prepare("SELECT id FROM companies WHERE name = ?");
        $check->execute([$companyName]);

        if ($check->fetch()) {
            $popupMessage = "Company already exists.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO companies (name) VALUES (?)");
            $stmt->execute([$companyName]);
            $popupMessage = "Company \"$companyName\" created successfully.";
        }
        $showPopup = true;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Create Company</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: url('wp3519537.webp') no-repeat center center fixed;
      background-size: cover;
      margin: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      color: white;
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

    .popup-box button {
      background-color: #2e7d32;
      border: none;
      color: white;
      padding: 10px 20px;
      margin-top: 20px;
      border-radius: 8px;
      cursor: pointer;
    }

    .popup-box button:hover {
      background-color: #1b5e20;
    }
  </style>
</head>
<body>

<?php if ($showPopup): ?>
  <div class="popup-overlay">
    <div class="popup-box">
      <p><?= htmlspecialchars($popupMessage) ?></p>
      <form method="get" action="admin_panel.php">
        <button type="submit">OK</button>
      </form>
    </div>
  </div>
<?php endif; ?>

</body>
</html>
