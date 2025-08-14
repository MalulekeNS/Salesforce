<?php
include 'db.php';

$data = json_decode(file_get_contents('php://input'), true);

$title = $data['title'];
$start = $data['start'];

// Extract date and time
$event_date = date('Y-m-d', strtotime($start));
$event_time = date('H:i:s', strtotime($start));

$stmt = $pdo->prepare("INSERT INTO programme_events (title, event_date, event_time) VALUES (?, ?, ?)");
$success = $stmt->execute([$title, $event_date, $event_time]);

echo json_encode(['success' => $success]);
?>
