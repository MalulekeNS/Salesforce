<?php
session_start();
include 'db.php';

// Fetch companies from DB
$stmt = $pdo->query("SELECT name FROM companies ORDER BY name ASC");
$companies = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Data Room - Companies</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      background: url('des.png') no-repeat center center;
      background-size: cover;
      color: #ffffff;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .top-buttons {
      position: fixed;
      top: 20px;
      left: 20px;
      display: flex;
      gap: 15px;
      z-index: 999;
    }

    .top-buttons a,
    .top-buttons button {
      background-color: #0078D4;;
      color: white;
      text-decoration: none;
      padding: 10px 15px;
      border-radius: 8px;
      font-size: 14px;
      border: none;
      display: flex;
      align-items: center;
      gap: 5px;
      cursor: pointer;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
    }

    .top-buttons a:hover,
    .top-buttons button:hover {
      background-color: #62BF04;
    }

    .overlay {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(20px);
      padding: 40px;
      border-radius: 16px;
      max-width: 1200px;
      width: 95%;
      text-align: center;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.37);
    }

    h1 {
      font-size: 36px;
      margin-bottom: 30px;
      color: #FFD700;
    }

    .grid-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      gap: 30px;
      justify-items: center;
    }

    .company-card {
      background: linear-gradient(135deg, #0078D4, #030736);
      padding: 20px;
      border-radius: 12px;
      text-align: center;
      font-weight: bold;
      cursor: pointer;
      text-decoration: none;
      color: white;
      transition: transform 0.2s ease, box-shadow 0.3s ease;
      width: 100%;
      max-width: 150px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
    }

 .company-card:hover {
    background: #62BF04; /* Override the gradient completely */
    transform: translateY(-3px);
}


}


    @media (max-width: 768px) {
      .top-buttons {
        flex-direction: column;
        left: 10px;
        top: 10px;
      }
    }
  </style>
</head>
<body>

  <div class="top-buttons">
    <button onclick="history.back()"><i class="fas fa-arrow-left"></i> Back</button>
    <a href="index.php"><i class="fas fa-home"></i> Home</a>
  </div>

  <div class="overlay">
    <h1>Select a Company to View Data Room</h1>

    <div class="grid-container">
      <?php foreach ($companies as $company): ?>
        <a class="company-card" href="data_room_company.php?name=<?= urlencode($company) ?>">
          <?= htmlspecialchars($company) ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>

</body>
</html>
