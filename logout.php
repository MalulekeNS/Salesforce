<?php
session_start();

if (isset($_POST['confirm'])) {
    session_unset();
    session_destroy();
    $loggedOut = true;
} else if (isset($_POST['cancel'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Logout</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: url('des.png') no-repeat center center;
      background-size: cover;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      color: white;
    }

    .popup {
      background: rgba(34, 51, 34, 0.3);
      border: 1px solid rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(10px);
      padding: 30px;
      border-radius: 16px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.2);
      text-align: center;
      max-width: 400px;
    }

    h2 {
      color: #d4af37;
      margin-bottom: 20px;
    }

    form {
      display: flex;
      justify-content: center;
      gap: 15px;
    }

    .btn {
      background-color: #2e7d32;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 8px;
      cursor: pointer;
    }

    .btn:hover {
      background-color: #11ad02;
    }
  </style>
</head>
<body>

<div class="popup">
  <?php if (isset($loggedOut)): ?>
    <h2>You have been logged out.</h2>
    <button class="btn" onclick="window.location.href='login.php'">Login Again</button>
  <?php else: ?>
    <h2>Are you sure you want to logout?</h2>
    <form method="post">
      <button type="submit" name="confirm" class="btn">Yes</button>
      <button type="submit" name="cancel" class="btn">No</button>
    </form>
  <?php endif; ?>
</div>

</body>
</html>
