<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

include 'db.php';

$popupMessage = '';
$showPopup = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventId = $_POST['event_id'] ?? null;

    if (!$eventId || !is_numeric($eventId)) {
        $popupMessage = "Invalid event ID.";
    } else {
        $stmt = $pdo->prepare("SELECT title FROM programme_events WHERE id = ?");
        $stmt->execute([$eventId]);
        $eventTitle = $stmt->fetchColumn();

        if ($eventTitle) {
            $pdo->prepare("DELETE FROM programme_events WHERE id = ?")->execute([$eventId]);
            $popupMessage = "Event \"$eventTitle\" deleted successfully.";
        } else {
            $popupMessage = "Event not found.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Delete Event</title>
  <style>
    body {
      background: url('wp3519537.webp') no-repeat center center fixed;
      background-size: cover;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      color: white;
      font-family: Arial, sans-serif;
    }
    .popup {
      background: white;
      color: black;
      padding: 30px 40px;
      border-radius: 12px;
      text-align: center;
      box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }
    .popup button {
      background-color: #2e7d32;
      border: none;
      color: white;
      padding: 10px 20px;
      margin-top: 20px;
      border-radius: 8px;
      cursor: pointer;
    }
    .popup button:hover {
      background-color: #1b5e20;
    }
  </style>
</head>
<body>
  <div class="popup">
    <h3><?= htmlspecialchars($popupMessage) ?></h3>
    <form method="get" action="admin_panel.php">
      <button type="submit">OK</button>
    </form>
  </div>
</body>
</html>
