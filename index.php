<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$name = $_SESSION['name'] ?? 'User';
$role = $_SESSION['role'] ?? 'user';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Program Portal</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background: url('des.png') no-repeat center center;
      background-size: cover;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .overlay {
      background-color: rgba(0, 0, 0, 0.6);
      padding: 40px;
      border-radius: 16px;
      text-align: center;
      max-width: 600px;
      width: 100%;
      position: relative;
    }

    h1, p, button {
      color: white;
    }

    h1 {
      font-size: 40px;
      font-weight: bold;
      margin: 0 0 30px;
    }

    h2 {
      color: black;
    }

    .grid {
      display: grid;
      grid-template-columns: repeat(2, 200px);
      gap: 30px;
      justify-content: center;
    }

    .grid button {
      padding: 20px;
      font-size: 17px;
      background-color: #62BF04;
      color: white;
      border: none;
      border-radius: 30px;
      cursor: pointer;
      box-shadow: 0 4px 6px rgba(0,0,0,0.3);
      transition: background-color 0.3s ease;
    }

    .grid button:hover {
      background-color: #025373;
    }

    .nav-controls {
      position: fixed;
      top: 20px;
      right: 30px;
      display: flex;
      gap: 20px;
      align-items: center;
      z-index: 100;
    }

    .nav-controls button,
    .nav-controls a {
      background-color: #62BF04;
      color: white;
      border: none;
      padding: 10px 15px;
      border-radius: 20px;
      cursor: pointer;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .nav-controls button:hover,
    .nav-controls a:hover {
      background-color: #025373;
    }

    .profile-menu {
      display: none;
      position: absolute;
      top: 45px;
      right: 0;
      background-color: none;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
      min-width: 150px;
      z-index: 200;
    }

    .profile-menu a {
      display: block;
      padding: 10px 15px;
      color: #ffffff;
      text-decoration: none;
      transition: background-color 0.2s ease;
    }

    .profile-menu a:hover {
      background-color: #025373; /* Gold on hover */
      color: white;
    }

    @media (max-width: 480px) {
      .grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>

  <!-- Profile Dropdown -->
  <div class="nav-controls">
    <div style="position: relative;">
      <button onclick="toggleProfileMenu()">
        <i class="fas fa-user-circle"></i> <?= htmlspecialchars($name) ?> <i class="fas fa-caret-down"></i>
      </button>
      <div class="profile-menu" id="profileMenu">
        <a href="profile.php">Profile</a>
        <?php if ($role === 'admin'): ?>
          <a href="admin_panel.php">Admin Panel</a>
        <?php endif; ?>
        <a href="logout.php">Logout</a>
      </div>
    </div>
  </div>

  <!-- Main UI -->
  <div class="overlay">
    <h1>African Bank</h1>

    <div class="grid">
      <button onclick="location.href='portfolio_dashboard.php'">Portfolio Reports</button>
      <button onclick="location.href='company_reports.php'">Company Reports</button>
      <button onclick="location.href='programme_events.php'">Programme Events</button>
      <button onclick="location.href='data_room_companies.php'">Data Room</button>
    </div>
  </div>

  <script>
    function toggleProfileMenu() {
      const menu = document.getElementById('profileMenu');
      menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    }

    document.addEventListener('click', function (e) {
      if (!e.target.closest('.nav-controls')) {
        document.getElementById('profileMenu').style.display = 'none';
      }
    });
  </script>

</body>
</html>
