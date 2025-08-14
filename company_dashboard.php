<?php
include 'db.php';

// Fetch data from portfolio_reports
$stmt = $pdo->query("SELECT name, report_type AS type, metric1 AS metric_a, metric2 AS metric_b, metric3 AS metric_c FROM portfolio_reports");
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Initialize totals
$totals = ['annual' => [0, 0, 0], 'quarterly' => [0, 0, 0], 'monthly' => [0, 0, 0], 'live' => [0, 0, 0]];

foreach ($data as $row) {
    $type = strtolower($row['type']);
    if (isset($totals[$type])) {
        $totals[$type][0] += (int)$row['metric_a'];
        $totals[$type][1] += (int)$row['metric_b'];
        $totals[$type][2] += (int)$row['metric_c'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Portfolio Dashboard</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
        /* Your existing CSS styles for layout */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: url('101.png') no-repeat left top fixed;
            background-size: 105%;
            background-position: 30px top;
            background-repeat: no-repeat;
            color: white;
            display: flex;
            height: 100vh;
        }

        .main {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
            background-color: rgba(0, 0, 0, 0.6);
        }

        .sidebar {
            width: 220px;
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(8px);
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

        .top-buttons {
            padding: 20px;
            display: flex;
            gap: 15px;
            align-items: center;
            background: rgba(0, 0, 0, 0.3);
        }

        .top-buttons a {
            background-color: #025373;
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        .top-buttons a:hover {
            background-color: #62BF04;
        }

        .chart-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            max-width: 1300px;
            margin: auto;
        }

        .chart-box {
            background: white;
            color: black;
            padding: 20px;
            border-radius: 10px;
            height: 320px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .scorecard {
            font-size: 28px;
            font-weight: bold;
            text-align: center;
            width: 130px;
            height: 130px;
            border-radius: 50%;
            background: #006400; /* Dark Green */
            color: white; /* Grey instead of White */
            display: flex;
            align-items: center;
            justify-content: center;
            margin: auto;
        }

        .task-list {
            list-style: none;
            padding: 0;
        }

        .task-list li {
            margin: 10px 0;
            background: #001f3f; /* Navy Blue */
            color: white; /* Grey instead of White */
            padding: 8px 12px;
            border-radius: 6px;
        }

        .actions {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }

        .actions a {
            background-color: #001f3f; /* Navy Blue */
            color: white; /* Grey instead of White */
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        .actions a:hover {
            background-color: #006400;
        }
    </style>
</head>
<body>

<div class="sidebar">
  <button onclick="loadReport('annual')"><i class="fas fa-calendar-alt"></i> Annual Reports</button>
  <button onclick="loadReport('quarterly')"><i class="fas fa-chart-line"></i> Quarterly Reports</button>
  <button onclick="loadReport('monthly')"><i class="fas fa-calendar"></i> Monthly Reports</button>
  <button onclick="loadReport('live')"><i class="fas fa-bolt"></i> Live Data</button>
  <button onclick="window.location.href='upload_report_data.php'"><i class="fas fa-upload"></i> Upload CSV Data</button>
</div>

<div class="main" id="reportContent">
  <div class="top-buttons">
    <a href="javascript:history.back()"><i class="fas fa-arrow-left"></i> Back</a>
    <a href="index.php"><i class="fas fa-home"></i> Home</a>
  </div>
  <h2></h2>
</div>
  <h2></h2>
</div>

<script>
const totals = <?= json_encode($totals) ?>;

function loadReport(type) {
  const values = totals[type] || [0, 0, 0];
  const percent = Math.min(100, Math.floor((values.reduce((a,b)=>a+b)/30)*10));
  const container = document.getElementById('reportContent');

  container.innerHTML = `
    <div class="top-buttons">
      <a href="javascript:history.back()"><i class="fas fa-arrow-left"></i> Back</a>
      <a href="index.php"><i class="fas fa-home"></i> Home</a>
    </div>
    <h2>${type.charAt(0).toUpperCase() + type.slice(1)} Combined Report</h2>
    <div class="chart-grid">
      <div class="chart-box"><canvas id="chart1"></canvas></div>
      <div class="chart-box"><canvas id="chart2"></canvas></div>
      <div class="chart-box">
        <div class="scorecard">${percent}%</div>
        <p style='color:#006400;font-weight:bold;text-align:center;margin-top:10px;'>Performance Level</p>
      </div>
      <div class="chart-box">
        <h3>Task List</h3>
        <ul class="task-list">
          <li>Review Financial Uploads</li>
          <li>Audit Company Submissions</li>
          <li>Generate Report PDFs</li>
          <li>Plan Quarterly Reviews</li>
        </ul>
      </div>
      <div class="chart-box" style="grid-column: span 2;"><canvas id="chart3"></canvas></div>
    </div>
    <div class="actions">
      <a href="pdfs/combined/${type}.pdf" target="_blank">View PDF</a>
      <a href="#" onclick="downloadPDF()">Download</a>
    </div>
  `;

  const chart1Ctx = document.getElementById('chart1').getContext('2d');
  const chart2Ctx = document.getElementById('chart2').getContext('2d');
  const chart3Ctx = document.getElementById('chart3').getContext('2d');

  // Gradient for Doughnut
  const gradient1 = chart1Ctx.createLinearGradient(0, 0, 200, 0);
  gradient1.addColorStop(0, '#001f3f'); 
  gradient1.addColorStop(1, '#006400'); 

  new Chart(chart1Ctx, {
    type: 'doughnut',
    data: {
      labels: ['Metric A', 'Metric B', 'Metric C'],
      datasets: [{
        data: values,
        backgroundColor: [gradient1, gradient1, gradient1]
      }]
    },
    options: { cutout: '60%' }
  });

  // Gradient for Bar
  const gradient2 = chart2Ctx.createLinearGradient(0, 0, 0, 300);
  gradient2.addColorStop(0, '#001f3f');
  gradient2.addColorStop(1, '#006400');

  new Chart(chart2Ctx, {
    type: 'bar',
    data: {
      labels: ['Metric A', 'Metric B', 'Metric C'],
      datasets: [{
        label: 'Metrics',
        data: values,
        backgroundColor: gradient2,
        barThickness: 25
      }]
    }
  });

  // Radar Chart with subtle fill
  new Chart(chart3Ctx, {
    type: 'radar',
    data: {
      labels: ['Strategy', 'Revenue', 'Growth', 'Efficiency', 'Impact'],
      datasets: [{
        label: 'Score',
        data: [20, 40, 30, 35, 45],
        backgroundColor: 'rgba(0, 31, 63, 0.3)',
        borderColor: '#001f3f',
        pointBackgroundColor: '#006400'
      }]
    }
  });
}

function downloadPDF() {
  const content = document.getElementById('reportContent');
  html2pdf().from(content).save('Portfolio_Reports.pdf');
}
</script>

</body>
</html>
