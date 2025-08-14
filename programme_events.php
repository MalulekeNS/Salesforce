<?php
session_start();
include 'db.php';

// Fetch events from the database
$stmt = $pdo->query("SELECT title, event_date, event_time, description FROM programme_events ORDER BY event_date ASC");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Programme Events</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    /* [All your existing CSS remains unchanged] */
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background: url('des.png') no-repeat center center;
      background-size: cover;
      color: white;
      display: flex;
      flex-direction: row;
      height: 100vh;
    }
.sidebar {
      width: 220px;
      background: rgba(0, 0, 0, 0.3);
      backdrop-filter: (2px);
      padding: 30px;
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    .sidebar button {
      background-color: #e1f2e3;
      color: black;
      border: none;
      padding: 15px 25px;
      border-radius: 20px;
      text-align: left;
      cursor: pointer;
      font-size: 15px;
      transition: background-color 0.3s ease;
    }

    .sidebar button:hover {
      background-color: #62BF04;
    }
    .content-wrapper {
      flex: 1;
      display: flex;
      flex-direction: column;
    }
    .top-buttons {
      padding: 20px;
      display: flex;
      gap: 15px;
      align-items: center;
      background: rgba(0, 0, 0, 0.3);
    }
    .top-buttons button,
    .top-buttons a {
      background-color: #025373;
      color: white;
      border: none;
      padding: 10px 15px;
      border-radius: 8px;
      cursor: pointer;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 5px;
      font-size: 14px;
    }
    .top-buttons button:hover,
    .top-buttons a:hover {
      background-color: #62BF04;
    }
    .overlay {
      background-color: rgba(0, 0, 0, 0.6);
      padding: 30px;
      border-radius: 16px;
      margin: 20px;
      flex-grow: 1;
      overflow-y: auto;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
    }
    h1 {
      font-size: 32px;
      text-align: center;
      cursor: pointer;
      color: #d4af37;
      margin-bottom: 20px;
    }
    .event-box {
      background-color: rgba(255, 255, 255, 0.15);
      padding: 20px;
      border-radius: 10px;
      margin-bottom: 20px;
      color: white;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }
    .event-box h3 {
      margin: 0 0 10px;
      font-size: 20px;
      color: #d4af37;
    }
    .event-box p.date {
      margin: 0 0 10px;
      font-style: italic;
      color: #ccc;
    }
    .calendar-box {
      background-color: rgba(255, 255, 255, 0.1);
      padding: 20px;
      margin-bottom: 30px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    }
    .calendar-box h3 {
      color: #fff;
      margin-top: 0;
      margin-bottom: 15px;
    }
    .calendar-box table {
      width: 100%;
      border-collapse: collapse;
      color: white;
    }
    .calendar-box th, .calendar-box td {
      padding: 10px;
      text-align: center;
      border: 1px solid rgba(255,255,255,0.2);
    }
    .calendar-box th {
      background-color: rgba(46, 125, 50, 0.8);
    }
    .today {
      background-color: white;
      color: black;
      font-weight: bold;
    }
    @media (max-width: 768px) {
      .top-buttons {
        flex-direction: column;
        align-items: flex-start;
      }
    }
  </style>
</head>
<body>

  <div class="sidebar">
    <button onclick="location.href='programme_events.php'">Programme Events</button>
    <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.2); margin: 10px 0;">
    <button>Announcements</button>
    <button>Upcoming Events</button>
    <button>Past Events</button>
    <button>Calendar</button>
  </div>

  <div class="content-wrapper">
    <div class="top-buttons">
      <button onclick="history.back()"><i class="fas fa-arrow-left"></i> Back</button>
      <a href="index.php"><i class="fas fa-home"></i> Home</a>
    </div>

    <div class="overlay">
      <div class="calendar-box">
        <h3 id="calendarTitle">Calendar</h3>
        <table id="calendarTable">
          <thead>
            <tr>
              <th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th>
              <th>Thu</th><th>Fri</th><th>Sat</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>

      <h1 onclick="toggleEvents()"><i class="fas fa-calendar-alt"></i> Programme Events</h1>

      <div id="eventList" style="display: none;">
        <?php if (count($events) > 0): ?>
          <?php foreach ($events as $event): ?>
            <div class="event-box">
              <h3><?= htmlspecialchars($event['title']) ?></h3>
              <p class="date"><?= date("d F Y", strtotime($event['event_date'])) ?> • <?= htmlspecialchars($event['event_time']) ?></p>
              <p><?= htmlspecialchars($event['description']) ?></p>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p style="text-align:center;">No events available.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <script>
    function toggleEvents() {
      const list = document.getElementById('eventList');
      list.style.display = list.style.display === 'none' ? 'block' : 'none';
    }

    // Calendar rendering logic [Unchanged]
    const now = new Date();
    const month = now.getMonth();
    const year = now.getFullYear();
    const today = now.getDate();
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const monthNames = [
      "January", "February", "March", "April", "May", "June",
      "July", "August", "September", "October", "November", "December"
    ];

    document.getElementById("calendarTitle").innerText = `${monthNames[month]} ${year}`;
    const tbody = document.querySelector("#calendarTable tbody");
    let row = document.createElement('tr');

    for (let i = 0; i < firstDay; i++) {
      row.innerHTML += "<td></td>";
    }

    for (let day = 1; day <= daysInMonth; day++) {
      const cell = document.createElement("td");
      cell.innerText = day;
      if (day === today) {
        cell.classList.add("today");
      }
      row.appendChild(cell);
      if ((firstDay + day) % 7 === 0 || day === daysInMonth) {
        tbody.appendChild(row);
        row = document.createElement("tr");
      }
    }
  </script>

</body>
</html>
