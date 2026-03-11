<?php
// backend/update-request-status.php
require_once '../includes/functions.php';

if (!is_admin_logged_in()) {
    error_response('Unauthorized access');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_response('Invalid request method');
}

$requestId = intval($_POST['request_id'] ?? 0);
$status = clean_input($_POST['status'] ?? '');

$allowedStatuses = ['pending', 'confirmed', 'completed', 'cancelled'];

if (!$requestId || !in_array($status, $allowedStatuses)) {
    error_response('Invalid data provided');
}

try {
    $stmt = $pdo->prepare("UPDATE service_requests SET status = ? WHERE request_id = ?");
    $stmt->execute([$status, $requestId]);

    if ($stmt->rowCount() > 0) {
        log_activity($pdo, 'Status Update', "Updated request #$requestId status to $status", $_SESSION['admin_id']);
        success_response("Request status updated to $status successfully.");
    } else {
        error_response("No changes made or request not found.");
    }
} catch (Exception $e) {
    error_log("Update status error: " . $e->getMessage());
    error_response("Database error occurred.");
}
?>