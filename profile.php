<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

$userId = $_SESSION['user_id'];
$message = '';

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    die("User not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newName = trim($_POST['name']);
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';

    $changes = [];

    if ($newName && $newName !== $user['name']) {
        $stmt = $pdo->prepare("UPDATE users SET name = ? WHERE id = ?");
        $stmt->execute([$newName, $userId]);
        $_SESSION['name'] = $newName;
        $message = "Name updated successfully.";
        $changes[] = "name";
    }

    if (!empty($newPassword)) {
        if (password_verify($currentPassword, $user['password'])) {
            $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashed, $userId]);
            $message .= " Password changed.";
            $changes[] = "password";
        } else {
            $message .= " Current password incorrect.";
        }
    }

    if (!empty($changes)) {
        $changedItems = implode(" and ", $changes);
        $subject = "Profile Updates";
        $emailMessage = "User ({$user['name']}) has changed {$changedItems}.";
        mail("newcayen@teksaholdings.co.za", $subject, $emailMessage);

        $stmt = $pdo->prepare("INSERT INTO audit_logs (user_id, username, action) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $user['name'], $emailMessage]);
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Profile</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    body {
      font-family: Arial, sans-serif;
      background: url('des.png') no-repeat center center;
      background-size: cover;
      margin: 0;
      padding: 40px;
      color: white;
    }

    .top-buttons {
      position: fixed;
      top: 20px;
      left: 20px;
      display: flex;
      gap: 15px;
      z-index: 999;
    }

    .top-buttons a {
      background-color: #0078D4;
      color: white;
      border: none;
      padding: 10px 15px;
      border-radius: 8px;
      cursor: pointer;
      text-decoration: none;
      font-size: 14px;
      display: flex;
      align-items: center;
      gap: 5px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
    }

    .top-buttons a:hover {
      background-color: #11ad02;
    }

    .profile-box {
      background: rgba(34, 51, 34, 0.3);
      border: 1px solid rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      max-width: 500px;
      margin: 100px auto 0;
      padding: 30px;
      border-radius: 16px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
    }

    h2 {
      margin-top: 0;
      color: #d4af37;
      text-align: center;
      text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      margin-bottom: 15px;
      border: none;
      border-radius: 8px;
      background-color: rgba(255,255,255,0.15);
      color: white;
    }

    input::placeholder {
      color: #ddd;
    }

    .btn {
      background: #2e7d32;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      width: 100%;
    }

    .btn:hover {
      background: #050f05;
    }

    .msg {
      margin-top: 15px;
      background-color: rgba(0, 128, 0, 0.7);
      padding: 10px 15px;
      border-radius: 10px;
      color: white;
      font-weight: bold;
      text-align: center;
    }
  </style>
</head>
<body>

  <div class="top-buttons">
    <a href="javascript:history.back()"><i class="fas fa-arrow-left"></i> Back</a>
    <a href="index.php"><i class="fas fa-home"></i> Home</a>
  </div>

  <div class="profile-box">
    <h2>My Profile</h2>

    <?php if ($message): ?>
      <div class="msg"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
      <label>Name:</label>
      <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>

      <label>Email:</label>
      <input type="email" value="<?= htmlspecialchars($user['email']) ?>" readonly>

      <label>Role:</label>
      <input type="text" value="<?= $user['role'] ?>" readonly>

      <label>Current Password (required for password change):</label>
      <input type="password" name="current_password">

      <label>New Password:</label>
      <input type="password" name="new_password">

      <button type="submit" class="btn">Update Profile</button>
    </form>
  </div>

</body>
</html>
