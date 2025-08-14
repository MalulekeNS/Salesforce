<?php
include 'db.php';

$popupMessage = '';
$showPopup = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';

    if (!$email || !$newPassword) {
        $popupMessage = "Email and new password are required.";
        $showPopup = true;
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            $popupMessage = "No user found with that email.";
            $showPopup = true;
        } else {
            $update = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
            $update->execute([$newPassword, $email]);
            $popupMessage = "Password updated successfully.";
            $showPopup = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Password</title>
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
    }

    .login-container {
      background: rgba(34, 51, 34, 0.3);
      border: 1px solid rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(10px);
      padding: 30px;
      border-radius: 16px;
      width: 320px;
      text-align: center;
      color: white;
      box-shadow: 0 4px 30px rgba(0,0,0,0.2);
    }

    input[type="email"], input[type="password"] {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: none;
      border-radius: 8px;
      background-color: rgba(255,255,255,0.15);
      color: white;
    }

    input::placeholder {
      color: #ddd;
    }

    button {
      padding: 10px 20px;
      background-color: #164018;
      border: none;
      color: white;
      border-radius: 8px;
      cursor: pointer;
      width: 100%;
    }

    button:hover {
      background-color: #071408;
    }

    .links {
      margin-top: 15px;
    }

    .links a {
      color: #d4af37;
      text-decoration: none;
    }

    .links a:hover {
      text-decoration: underline;
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
      <button onclick="window.location.href='login.php'">OK</button>
    </div>
  </div>
<?php endif; ?>

<div class="login-container">
  <h2>Reset Password</h2>
  <form method="post">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="new_password" placeholder="New Password" required>
    <button type="submit">Reset</button>
  </form>
  <div class="links">
    <a href="login.php">Back to Login</a>
  </div>
</div>

</body>
</html>
