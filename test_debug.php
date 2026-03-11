<?php
// test_debug.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/config.php';
require_once 'includes/functions.php';

echo "<h1>Debug Report</h1>";

// 1. Check DB Connection
try {
    if ($pdo) {
        echo "<p style='color:green'>✅ Database Connection Successful</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>❌ Database Connection Failed: " . $e->getMessage() . "</p>";
    die();
}

// 2. Check Admin User
$stmt = $pdo->query("SELECT * FROM admin_users WHERE username = 'admin'");
$admin = $stmt->fetch();

if ($admin) {
    echo "<p style='color:green'>✅ Admin user found: " . htmlspecialchars($admin['username']) . "</p>";
    echo "<p>Hash in DB: " . $admin['password_hash'] . "</p>";

    // 3. Verify Password
    $password_to_test = 'Admin@123';
    if (password_verify($password_to_test, $admin['password_hash'])) {
        echo "<p style='color:green'>✅ Password 'Admin@123' matches hash!</p>";
    } else {
        echo "<p style='color:red'>❌ Password 'Admin@123' DOES NOT match hash.</p>";
        // Generate new one
        $new_hash = password_hash($password_to_test, PASSWORD_BCRYPT);
        echo "<p>Recommended Hash: $new_hash</p>";
    }
} else {
    echo "<p style='color:red'>❌ Admin user 'admin' NOT found in table 'admin_users'.</p>";
}

// 4. Check Session
echo "<p>Session Status: " . session_status() . "</p>";
echo "<p>Session ID: " . session_id() . "</p>";
?>