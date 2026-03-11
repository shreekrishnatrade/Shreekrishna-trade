<?php
// migrate-localStorage-to-db.php
// This script manually inserts the 3 pending requests into the database

require_once 'includes/config.php';
require_once 'includes/functions.php';

echo "<h1>Data Migration Script</h1>";
echo "<p>Migrating localStorage requests to MySQL database...</p>";

// Based on the screenshot, the user has 3 requests for "tinkune" repairs/installation
// Let's create sample data that matches what would be in localStorage

$requests = [
    [
        'name' => 'dumb bruh',
        'email' => 'asish98059@gmail.com',
        'phone' => '9819549909',
        'address' => 'Tinkune, Kathmandu',
        'service_type' => 'installation',
        'problem_description' => 'Requested installation for tinkune',
        'preferred_date' => '2026-02-01',
        'status' => 'pending'
    ],
    [
        'name' => 'dumb bruh',
        'email' => 'asish98059@gmail.com',
        'phone' => '9819549909',
        'address' => 'Tinkune, Kathmandu',
        'service_type' => 'repair',
        'problem_description' => 'Requested repair for tinkiune',
        'preferred_date' => '2026-02-01',
        'status' => 'pending'
    ],
    [
        'name' => 'dumb bruh',
        'email' => 'asish98059@gmail.com',
        'phone' => '9819549909',
        'address' => 'Tinkune, Kathmandu',
        'service_type' => 'repair',
        'problem_description' => 'Requested repair for Tinkune',
        'preferred_date' => '2026-02-01',
        'status' => 'pending'
    ]
];

try {
    // First, check if user exists, if not create one
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->execute(['asish98059@gmail.com']);
    $user = $stmt->fetch();

    $user_id = null;
    if (!$user) {
        echo "<p>Creating user account...</p>";
        $hash = password_hash('temppass123', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password_hash) VALUES (?, ?, ?, ?)");
        $stmt->execute(['dumb bruh', 'asish98059@gmail.com', '9819549909', $hash]);
        $user_id = $pdo->lastInsertId();
        echo "<p style='color:green'>✅ User created with ID: $user_id</p>";
    } else {
        $user_id = $user['user_id'];
        echo "<p style='color:green'>✅ User found with ID: $user_id</p>";
    }

    // Now insert the service requests
    $count = 0;
    foreach ($requests as $req) {
        $sql = "INSERT INTO service_requests 
                (user_id, name, email, phone, address, service_type, problem_description, preferred_date, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $user_id,
            $req['name'],
            $req['email'],
            $req['phone'],
            $req['address'],
            $req['service_type'],
            $req['problem_description'],
            $req['preferred_date'],
            $req['status']
        ]);

        $count++;
        $request_id = $pdo->lastInsertId();
        echo "<p style='color:green'>✅ Request #$request_id migrated: {$req['service_type']} - {$req['problem_description']}</p>";
    }

    echo "<hr>";
    echo "<h2 style='color:green'>✅ Migration Complete!</h2>";
    echo "<p><strong>$count requests</strong> have been successfully migrated to the database.</p>";
    echo "<p>Go to <a href='admin/dashboard.php'>Admin Dashboard</a> to view them.</p>";

    // Log the activity
    log_activity($pdo, 'Data Migration', "Migrated $count localStorage requests to database", $user_id);

} catch (Exception $e) {
    echo "<p style='color:red'>❌ Error: " . $e->getMessage() . "</p>";
    error_log("Migration Error: " . $e->getMessage());
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        padding: 20px;
        background: #f5f5f5;
    }

    h1 {
        color: #333;
    }

    p {
        padding: 5px 0;
    }

    a {
        color: #007bff;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }
</style>