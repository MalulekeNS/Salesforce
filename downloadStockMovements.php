<?php
// Sample data - replace this with your actual data retrieval method (e.g., from a database)
$stockMovements = [
    ['Date', 'Item Name', 'Quantity', 'Movement Type'],
    ['2024-01-01', 'Product A', '10', 'IN'],
    ['2024-01-02', 'Product B', '5', 'OUT'],
    ['2024-01-03', 'Product C', '20', 'IN'],
    ['2024-01-04', 'Product A', '7', 'OUT'],
];

// Set the headers to prompt for download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="stock_movements.csv"');

// Open output stream
$output = fopen('php://output', 'w');

// Write data to CSV
foreach ($stockMovements as $movement) {
    fputcsv($output, $movement);
}

// Close output stream
fclose($output);
exit();
?>
