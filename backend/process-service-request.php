<?php
// backend/process-service-request.php

require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_response('Invalid Method');
}

$user_id = null;
if (is_logged_in()) {
    $user_id = $_SESSION['user_id'];
}

$name = clean_input($_POST['name'] ?? '');
$email = clean_input($_POST['email'] ?? '');
$phone = clean_input($_POST['phone'] ?? '');
$address = clean_input($_POST['address'] ?? '');
$service = clean_input($_POST['service'] ?? ''); // Maps to service_type
// Frontend form (service-request.html) uses name="service", schema uses service_type
// I will map 'service' to 'service_type'
$details = clean_input($_POST['message'] ?? ''); // Maps to problem_description
$date = clean_input($_POST['reqDate'] ?? '');
// Time might be extracted from datetime-local or separate
$time = null;
if (strpos($date, 'T') !== false) {
    // splits 2023-10-10T14:30
    $parts = explode('T', $date);
    $date = $parts[0];
    $time = $parts[1];
}

if (empty($name) || empty($phone) || empty($service)) {
    error_response('Please fill required fields (Name, Phone, Service)');
}

try {
    // If user not logged in, try to find user by email to link? 
    // Spec says: "Check if user exists (by email), link to user_id if found"
    if (!$user_id) {
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $u = $stmt->fetch();
        if ($u)
            $user_id = $u['user_id'];
    }

    $sql = "INSERT INTO service_requests (user_id, name, email, phone, address, service_type, problem_description, preferred_date, preferred_time, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id, $name, $email, $phone, $address, $service, $details, $date, $time]);

    $req_id = $pdo->lastInsertId();

    // Log
    log_activity($pdo, 'Service Request', "New Request #$req_id by $name", $user_id);

    // Send Email
    $subject = "Service Request Received - #" . $req_id;
    $message = "
    <h2>Request Received</h2>
    <p>Dear $name,</p>
    <p>We have received your request for <strong>$service</strong>.</p>
    <p>We will contact you shortly at $phone.</p>
    ";
    send_email($email, $subject, $message);

    success_response('Service Request Submitted Successfully!', ['request_id' => $req_id]);

} catch (Exception $e) {
    error_log($e->getMessage());
    error_response('Database Error: Could not save request');
}
?>