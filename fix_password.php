<?php
// fix_password.php
require_once 'includes/config.php';
require_once 'includes/functions.php';

try {
    $new_hash = password_hash('Admin@123', PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("UPDATE admin_users SET password_hash = ? WHERE username = 'admin'");
    $stmt->execute([$new_hash]);

    echo "Password updated successfully to: " . $new_hash . "\n";

    // Verify immediately
    if (password_verify('Admin@123', $new_hash)) {
        echo "VERIFICATION: PASS\n";
    } else {
        echo "VERIFICATION: FAIL (Something is wrong with hashing)\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>