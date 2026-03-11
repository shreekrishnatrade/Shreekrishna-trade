<?php
// backend/process-admin-login.php

require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_response('Invalid Request Method');
}

$username = clean_input($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    error_response('Please enter username and password');
}

try {
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && verify_password($password, $admin['password_hash'])) {
        if (!$admin['is_active']) {
            error_response('Admin account inactive');
        }

        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_name'] = $admin['full_name'];
        $_SESSION['admin_role'] = $admin['role'];

        // Update Last Login
        $upd = $pdo->prepare("UPDATE admin_users SET last_login = NOW() WHERE admin_id = ?");
        $upd->execute([$admin['admin_id']]);

        log_activity($pdo, 'Admin Login', "Admin logged in: " . $admin['username'], null, $admin['admin_id']);

        success_response('Login Successful', ['redirect' => '../admin/dashboard.php']); // Path relative to frontend fetch usually, but backend returns string path
    } else {
        error_response('Invalid credentials');
    }

} catch (Exception $e) {
    error_log($e->getMessage());
    error_response('Server error during login');
}
?>