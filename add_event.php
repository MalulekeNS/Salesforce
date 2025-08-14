<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

include 'db.php';

$popupMessage = '';
$showPopup = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $eventDate = $_POST['event_date'] ?? '';
    $eventTime = $_POST['event_time'] ?? '';
    $description = trim($_POST['description'] ?? '');

    if (!$title || !$eventDate || !$eventTime || !$description) {
        $popupMessage = "All fields are required.";
        $showPopup = true;
    } else {
        $stmt = $pdo->prepare("INSERT INTO programme_events (title, event_date, event_time, description) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $eventDate, $eventTime, $description]);
        $popupMessage = "Event added successfully.";
        $showPopup = true;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Add Event</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
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
      color: white;
    }

    .form-container {
      background-color: rgba(0,0,0,0.6);
      padding: 30px;
      border-radius: 12px;
      width: 400px;
    }

    h2 {
      text-align: center;
      color: #d4af37;
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin-bottom: 6px;
      font-weight: bold;
    }

    input, textarea {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: none;
      border-radius: 6px;
      background-color: rgba(255,255,255,0.1);
      color: white;
    }

    button {
      width: 100%;
      background-color: #2e7d32;
      border: none;
      color: white;
      padding: 10px;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
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

<div class="form-container">
  <h2>Add New Event</h2>
  <form method="post">
    <label>Event Title</label>
    <input type="text" name="title" required>

    <label>Date</label>
    <input type="date" name="event_date" required>

    <label>Time</label>
    <input type="time" name="event_time" required>

    <label>Description</label>
    <textarea name="description" rows="4" required></textarea>

    <button type="submit">Add Event</button>
  </form>
</div>

</body>
</html>
