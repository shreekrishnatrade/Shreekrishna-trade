<?php
// includes/config.php

// Change to (use YOUR actual values):
define('DB_HOST', 'localhost');
define('DB_USER', 'ifq_41268545');  // Your database user from the error message
define('DB_PASS', 'YOUR_DATABASE_PASSWORD');  // Your actual database password
define('DB_NAME', 'YOUR_DATABASE_NAME');  // The database you selected in phpMyAdmin
define('SITE_URL', 'https://YOUR_ACTUAL_DOMAIN.com');  // Your hosting domain

// Site Constants
define('SITE_URL', 'http://localhost/shreekrishna2.0'); // Update this for production
define('SITE_NAME', 'Shree Krishna Services');
define('ADMIN_EMAIL', 'admin@shreekrishna.com');

// Email Configuration (SMTP)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com'); // Configure this
define('SMTP_PASS', 'your-app-password'); // Configure this

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Error Reporting (Turn off for production)
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../error.log');

// Session Configuration
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database Connection (PDO)
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Return JSON error if connection fails (since mostly API context)
    header('Content-Type: application/json');
    die(json_encode(['success' => false, 'message' => 'Database Connection Failed']));
}
?>
