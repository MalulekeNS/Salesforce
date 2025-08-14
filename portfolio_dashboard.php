<?php
// Enable full error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db.php';

// Fetch data from credit_rating_scores
$stmt = $pdo->query("SELECT domain, raw_score, justification_for_achievement_score, weight_percent, contribution_percent, justification_for_strategic_weighting FROM credit_rating_scores");
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Initialize totals
$totals = ['annual' => [0, 0, 0], 'quarterly' => [0, 0, 0], 'monthly' => [0, 0, 0], 'live' => [0, 0, 0]];

foreach ($data as $row) {
    $totals['annual'][0] += (int)$row['raw_score'];
    $totals['annual'][1] += (int)$row['weight_percent'];
    $totals['annual'][2] += (int)$row['contribution_percent'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Portfolio Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
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
            background: #103b09; /* Navy Blue */
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

<script>
const totals = <?= json_encode($totals) ?>;
const data = <?= json_encode($data) ?>;

function loadReport(type) {
  const container = document.getElementById('reportContent');

  const domains = ['Market & Customer', 'Operational', 'Governance & Compliance', 'Financial', 'Technology'];
  const domainScores = [11, 10, 8, 7, 3];
  const ehsScores = [39, 61];
  const values = totals[type] || [0, 0, 0];
  const percent = Math.min(100, Math.floor((values.reduce((a,b)=>a+b)/30)*10));

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
          <li>Verify Domain Inputs</li>
          <li>Review Scoring Justification</li>
          <li>Validate Weight Distribution</li>
          <li>Document Strategic Insights</li>
        </ul>
      </div>
      <div class="chart-box" style="grid-column: span 2;"><canvas id="chart3"></canvas></div>
    </div>
    <div class="actions">
      <a href="pdfs/combined/${type}.pdf" target="_blank">View PDF</a>
      <a href="#" onclick="downloadPDF()">Download</a>
    </div>
  `;

  new Chart(document.getElementById('chart1'), {
    type: 'bar',
    data: {
      labels: domains,
      datasets: [{
        label: 'Domains',
        data: domainScores,
        backgroundColor: ['#1E90FF', '#FF6347', '#FF8C00', '#800080', '#8A2BE2'],
        barThickness: 25
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: { beginAtZero: true },
        x: {
          ticks: {
            font: { weight: 'bold', size: 14, family: 'Arial' },
            color: '#000000'
          }
        }
      },
      plugins: {
        datalabels: {
          anchor: 'end',
          align: 'top',
          formatter: (value) => value + '%',
          font: { weight: 'bold' },
          color: 'black'
        }
      },
      legend: { display: false }
    },
    plugins: [ChartDataLabels]
  });

  new Chart(document.getElementById('chart2'), {
    type: 'doughnut',
    data: {
      labels: ['Enterprise Health Score (EHS)'],
      datasets: [{
        data: ehsScores,
        backgroundColor: ['#001f3f', '#D3D3D3']
      }]
    },
    options: {
      cutout: '60%',
      plugins: {
        datalabels: {
          formatter: (value) => value + '%',
          font: { weight: 'bold' },
          color: (ctx) => ctx.dataIndex === 0 ? '#FFFFFF' : '#000000',
          align: 'center',
          display: (ctx) => ctx.dataIndex === 0
        }
      }
    },
    plugins: [ChartDataLabels]
  });

  new Chart(document.getElementById('chart3'), {
    type: 'radar',
    data: {
      labels: ['Governance', 'Finance', 'Operations', 'Technology', 'Market'],
      datasets: [{
        label: 'Index',
        data: [21, 37, 30, 18, 42],
        backgroundColor: 'rgba(210, 180, 140, 0.3)',
        borderColor: '#D4AF37',
        pointBackgroundColor: '#D4AF37'
      }]
    },
    options: {
      plugins: {
        title: { display: true, text: 'Radar Chart (Strategic Domains)' }
      }
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
