<?php
// backend/logout.php
require_once '../includes/functions.php';

session_start();

if (is_admin_logged_in()) {
    log_activity($pdo, 'Admin Logout', 'Admin logged out', null, $_SESSION['admin_id']);
    $redirect = '../admin/login.html';
} elseif (is_logged_in()) {
    log_activity($pdo, 'Logout', 'User logged out', $_SESSION['user_id']);
    $redirect = '../index.html';
} else {
    $redirect = '../index.html';
}

session_destroy();
setcookie(session_name(), '', time() - 3600, '/');

header("Location: $redirect");
exit;
?>