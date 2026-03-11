<?php
// test_debug_simple.php
require_once 'includes/config.php';
// require_once 'includes/functions.php'; // functions requires config too, redundancy ok

try {
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch();

    if ($admin) {
        echo "ADMIN_FOUND\n";
        echo "HASH: " . $admin['password_hash'] . "\n";
        if (password_verify('Admin@123', $admin['password_hash'])) {
            echo "LOGIN_SUCCESS\n";
        } else {
            echo "LOGIN_FAIL\n";
            echo "NEW_HASH: " . password_hash('Admin@123', PASSWORD_BCRYPT) . "\n";
        }
    } else {
        echo "ADMIN_NOT_FOUND\n";
    }
} catch (PDOException $e) {
    echo "DB_ERROR: " . $e->getMessage() . "\n";
}
?>