<?php
// backend/process-login.php

require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_response('Invalid Request Method');
}

$email = clean_input($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    error_response('Please enter email and password');
}

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && verify_password($password, $user['password_hash'])) {
        if (!$user['is_active']) {
            error_response('Account is inactive. Please contact support.');
        }

        // Set Session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];

        log_activity($pdo, 'Login', "User logged in: " . $user['name'], $user['user_id']);

        success_response('Login Successful', ['redirect' => 'user/dashboard.php']);
    } else {
        error_response('Invalid email or password');
    }

} catch (Exception $e) {
    error_log($e->getMessage());
    error_response('Login failed due to server error');
}
?>