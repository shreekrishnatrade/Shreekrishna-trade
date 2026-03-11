<?php
// config.production.php - Production Configuration Template
// IMPORTANT: Rename this to config.php and update with your production values

// ==================== DATABASE CONFIGURATION ====================
define('DB_HOST', 'localhost');           // Your production DB host (e.g., localhost or IP)
define('DB_NAME', 'shreekrishna_services'); // Your database name
define('DB_USER', 'your_db_username');    // Your database username
define('DB_PASS', 'your_secure_password'); // Your database password

// ==================== SITE CONFIGURATION ====================
define('SITE_URL', 'https://yourdomain.com'); // Your production domain
define('SITE_NAME', 'Shree Krishna Services');
define('ADMIN_EMAIL', 'admin@yourdomain.com'); // Admin notification email

// ==================== SECURITY SETTINGS ====================
define('SECURE_COOKIES', true);  // Set to true for HTTPS
define('SESSION_LIFETIME', 3600); // 1 hour in seconds

// ==================== EMAIL CONFIGURATION (SMTP) ====================
// For production, use SMTP (Gmail, SendGrid, etc.)
define('SMTP_ENABLED', true);
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password'); // Use App Password for Gmail
define('SMTP_FROM_EMAIL', 'noreply@yourdomain.com');
define('SMTP_FROM_NAME', SITE_NAME);

// ==================== ERROR HANDLING ====================
// Set to false in production, true for debugging
define('DISPLAY_ERRORS', false);
define('LOG_ERRORS', true);

if (DISPLAY_ERRORS) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/error.log');
}

// ==================== TIMEZONE ====================
date_default_timezone_set('Asia/Kolkata');

// ==================== SESSION CONFIGURATION ====================
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', SECURE_COOKIES ? 1 : 0);
ini_set('session.cookie_samesite', 'Strict');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ==================== DATABASE CONNECTION ====================
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    if (DISPLAY_ERRORS) {
        die("Database connection failed: " . $e->getMessage());
    } else {
        die("Service temporarily unavailable. Please try again later.");
    }
}
?>