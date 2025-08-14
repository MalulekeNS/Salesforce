<?php
// Sample PHP code to insert data into the database
include 'db.php';

$records = [
    ['Business Profile', 60, 0.1, 'Legally registered, reactivated in 2022, strategic growth plans', 6.0],
    ['Financial Performance', 45, 0.1, 'Sound planning, but weak variance control, no bank references', 4.5],
    ['Repayment Behavior', 40, 0.1, 'No evidence of defaults, but also no bank references for past payments', 4.0],
    ['Operational Soundness', 40, 0.1, 'SOPs and dashboards not yet implemented; information flows weak', 4.0],
    ['Management & Governance', 42, 0.1, '42% readiness per As-Is analysis; no board changes yet', 4.2],
    // Add other records similarly...
];

foreach ($records as $record) {
    $stmt = $pdo->prepare("INSERT INTO credit_rating_scores (category, score_0_100, weight_percent, justification, weighted_score) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute($record);
}
?>
