<?php
// includes/functions.php

require_once 'config.php';

// Sanitize Input
function clean_input($data)
{
    if (is_array($data)) {
        return array_map('clean_input', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Validation Helpers
function validate_email($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validate_phone($phone)
{
    // Basic Indian phone validation: 10 digits
    return preg_match('/^[6-9]\d{9}$/', $phone);
}

// Password Security
function hash_password($password)
{
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

function verify_password($password, $hash)
{
    return password_verify($password, $hash);
}

function generate_token($length = 32)
{
    return bin2hex(random_bytes($length));
}

// Authentication Checks
function is_logged_in()
{
    return isset($_SESSION['user_id']);
}

function is_admin_logged_in()
{
    return isset($_SESSION['admin_id']);
}

function get_current_user_data($pdo)
{
    if (!is_logged_in())
        return null;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

// Activity Logging
function log_activity($pdo, $action, $description, $user_id = null, $admin_id = null)
{
    try {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        $agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, admin_id, action, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $admin_id, $action, $description, $ip, $agent]);
    } catch (Exception $e) {
        // Silently fail logging if DB issue to avoid breaking main flow
        error_log("Logging Error: " . $e->getMessage());
    }
}

// Formatting
function format_date($date, $format = 'd M Y, h:i A')
{
    return date($format, strtotime($date));
}

function format_currency($amount)
{
    return '₹' . number_format($amount, 2);
}

// Membership Helpers
function get_membership_status($pdo, $user_id)
{
    $stmt = $pdo->prepare("SELECT * FROM memberships WHERE user_id = ? AND is_active = 1 AND end_date >= CURDATE() ORDER BY end_date DESC LIMIT 1");
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}

function calculate_membership_end_date($plan_type, $start_date = 'now')
{
    $date = new DateTime($start_date);
    switch ($plan_type) {
        case 'basic':
            $date->modify('+3 months');
            break;
        case 'premium':
            $date->modify('+6 months');
            break;
        case 'vip':
            $date->modify('+12 months');
            break;
        default:
            $date->modify('+1 month'); // Fallback
    }
    return $date->format('Y-m-d');
}

// API Responses
function json_response($success, $message, $data = null)
{
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

function success_response($message, $data = null)
{
    json_response(true, $message, $data);
}

function error_response($message, $data = null)
{
    json_response(false, $message, $data);
}

// Email Sending (Mock or PHPMailer Placeholder)
function send_email($to, $subject, $message)
{
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: ' . SITE_NAME . ' <' . ADMIN_EMAIL . '>' . "\r\n";

    // In production, integrate PHPMailer here. 
    // Using native mail() for demonstration (requires local mail server setup).
    // Returns true to simulate success if standard mail() fails or isn't configured.
    return @mail($to, $subject, $message, $headers) || true;
}
?>