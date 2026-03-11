<?php
// backend/get-statistics.php

require_once '../includes/functions.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT * FROM site_statistics LIMIT 1");
    $stats = $stmt->fetch();

    if (!$stats) {
        $stats = [
            'satisfied_clients' => 500,
            'services_delivered' => 2000,
            'years_experience' => 10,
            'expert_technicians' => 15,
            'average_rating' => 4.8
        ];
    }

    echo json_encode([
        'success' => true,
        'data' => $stats
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'data' => []]);
}
?>