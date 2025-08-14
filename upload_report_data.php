<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Upload Report Data</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.4.1/papaparse.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('des.png') no-repeat center center;
            background-size: cover;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: rgba(0,0,0,0.6);
            padding: 30px;
            border-radius: 12px;
            width: 400px;
            text-align: center;
        }
        input[type="file"] {
            margin: 20px 0;
            color: white;
        }
        button {
            background-color: #2e7d32;
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
        }
        button:hover {
            background-color: #1b5e20;
        }
        .top-buttons {
            position: absolute;
            top: 20px;
            left: 20px;
            display: flex;
            gap: 15px;
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
        .popup-box {
            background: white;
            color: black;
            padding: 20px;
            border-radius: 10px;
            margin-top: 15px;
        }
    </style>
</head>
<body>

<div class="top-buttons">
    <a href="javascript:history.back()"><i class="fas fa-arrow-left"></i> Back</a>
    <a href="index.php"><i class="fas fa-home"></i> Home</a>
</div>

<div class="container">
    <h2>Upload Portfolio Report Data</h2>
    <input type="file" id="csvFile" accept=".csv" required>
    <br>
    <button onclick="handleUpload()">Upload CSV</button>
    <div id="feedback" class="popup-box" style="display:none;"></div>
    <p><a href="/mnt/data/portfolio_reports_template.csv" download style="color: #d4af37;">Download CSV Template</a></p>
</div>

<script>
function handleUpload() {
    const fileInput = document.getElementById('csvFile');
    const file = fileInput.files[0];

    if (!file) {
        alert("Please select a CSV file first.");
        return;
    }

    Papa.parse(file, {
        header: true,
        skipEmptyLines: true,
        complete: function(results) {
            const parsedData = results.data;
            localStorage.setItem('uploaded_csv_data', JSON.stringify(parsedData));
            document.getElementById('feedback').style.display = 'block';
            document.getElementById('feedback').innerHTML = `
                <strong>${parsedData.length} row(s)</strong> stored in browser storage.<br>
                You can now use this data in your dashboards or scripts.<br><br>
                <em>Storage Key:</em> <code>uploaded_csv_data</code>
            `;
        },
        error: function(error) {
            alert("Error parsing CSV: " + error.message);
        }
    });
}
</script>

</body>
</html>
