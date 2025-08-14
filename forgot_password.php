<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (!$email) {
        die("Email is required.");
    }

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hour

        $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE token = VALUES(token), expires_at = VALUES(expires_at)");
        $stmt->execute([$user['id'], $token, $expires]);

        $resetLink = "https://yourdomain.com/reset_password.php?token=$token";
        // mail($email, "Password Reset", "Click here: $resetLink"); // Enable in production
        echo "Password reset link sent. Check your email.";
    } else {
        echo "If the email exists, a reset link will be sent.";
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Forgot Password</title>
</head>
<body>
  <h2>Forgot Password</h2>
  <form method="post">
    <input type="email" name="email" placeholder="Enter your email" required><br>
    <button type="submit">Send Reset Link</button>
  </form>
</body>
</html>
