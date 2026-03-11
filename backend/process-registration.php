<?php
// backend/process-registration.php

require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_response('Invalid Request Method');
}

// Get POST data
$name = clean_input($_POST['name'] ?? '');
$email = clean_input($_POST['email'] ?? '');
$phone = clean_input($_POST['phone'] ?? '');
$password = $_POST['regPassword'] ?? ''; // Matching frontend ID usually or generic
// Frontend usually sends inputs matching name attribute. I will assume 'password' or adjust JS later. 
// Standardizing on 'password'. User request says "password", "confirm_password".
if (empty($password))
    $password = $_POST['password'] ?? '';

$address = clean_input($_POST['address'] ?? '');
$city = clean_input($_POST['city'] ?? '');
$pincode = clean_input($_POST['pincode'] ?? '');

// Validation
if (strlen($name) < 3)
    error_response('Name must be at least 3 characters');
if (!validate_email($email))
    error_response('Invalid Email Address');
// Phone validation relaxed for testing if needed, keeping simple check
if (strlen($phone) < 10)
    error_response('Phone number invalid');

if (strlen($password) < 6)
    error_response('Password must be at least 6 characters');

try {
    // Check if email Exists
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        error_response('Email already registered');
    }

    // Hash Password
    $hash = hash_password($password);

    // Insert User
    $token = generate_token();
    $sql = "INSERT INTO users (name, email, phone, password_hash, address, city, pincode, verification_token) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $email, $phone, $hash, $address, $city, $pincode, $token]);
    $user_id = $pdo->lastInsertId();

    // Auto Login
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_email'] = $email;

    // Send Email
    $verify_link = SITE_URL . "/backend/verify.php?token=$token"; // verify.php not requested but good to have link structure
    $subject = "Welcome to " . SITE_NAME;
    $message = "
    <h2>Welcome $name!</h2>
    <p>Your account has been created successfully.</p>
    <p><strong>Email:</strong> $email<br><strong>Phone:</strong> $phone</p>
    <p><a href='$verify_link'>Verify Your Email</a></p>
    ";
    send_email($email, $subject, $message);

    log_activity($pdo, 'Registration', "New user registered via form: $name ($email)", $user_id);

    success_response('Registration Successful', ['user_id' => $user_id, 'redirect' => 'user/dashboard.php']);

} catch (Exception $e) {
    error_log($e->getMessage());
    error_response('Registration failed due to server error');
}
?>