<?php
// backend/process-membership.php

require_once '../includes/functions.php';

if (!is_logged_in()) {
    error_response('Please login to purchase membership');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_response('Invalid Method');
}

$user_id = $_SESSION['user_id'];
$plan = clean_input($_POST['regPlan'] ?? ''); // From register form or membership form. Standardizing on 'regPlan' or 'plan_type'
if (empty($plan) || $plan === 'none')
    $plan = clean_input($_POST['plan_type'] ?? '');

if (empty($plan) || $plan === 'none') {
    error_response('Please select a valid plan');
}

// Config Plans
$plans = [
    'basic' => ['name' => 'Basic Plan', 'price' => 999],
    'silver' => ['name' => 'Silver Plan', 'price' => 1899], // Mapping UI 'silver' to DB 'premium' logic if needed, but schema said Basic, Premium, VIP.
    // Spec: Basic, Premium, VIP.
    // UI (register.html) had Basic, Silver, Gold.
    // I will map Silver->Premium, Gold->VIP for consistency with Spec/UI hybrid or stick to schema.
    // Let's support the UI values (basic, silver, gold) and map to schema enum if strict, or DB enum accepts them.
    // Schema Enum: basic, premium, vip.
    // I should map: silver->premium, gold->vip.
    'premium' => ['name' => 'Preferred Plan', 'price' => 1899],
    'vip' => ['name' => 'VIP Plan', 'price' => 3499],
    'gold' => ['name' => 'Gold Plan', 'price' => 3499] // treat gold as vip
];

// Mapping
$db_plan = $plan;
if ($plan === 'silver')
    $db_plan = 'premium';
if ($plan === 'gold')
    $db_plan = 'vip';

if (!in_array($db_plan, ['basic', 'premium', 'vip'])) {
    // Basic fallback or error?
    // If user chose "none" handled above.
    // If unknown, default to basic or error.
    // Assuming error for safety.
    if ($plan !== 'basic') {
        // Allow silver/gold pass through if schema was flexible, but schema is strict enum.
        // I will use mapping logic.
    }
}

$price = $plans[$plan]['price'] ?? 1000;
if ($plan === 'silver')
    $price = 1899;
if ($plan === 'gold')
    $price = 3499;
if ($plan === 'basic')
    $price = 999;

$start_date = date('Y-m-d');
$end_date = calculate_membership_end_date($db_plan, $start_date);
$txn_id = 'TXN-' . time() . '-' . rand(1000, 9999);

try {
    // Deactivate old
    $pdo->prepare("UPDATE memberships SET is_active = 0 WHERE user_id = ?")->execute([$user_id]);

    // Insert new
    $sql = "INSERT INTO memberships (user_id, plan_type, plan_name, price, start_date, end_date, payment_status, payment_method, transaction_id, is_active) VALUES (?, ?, ?, ?, ?, ?, 'completed', 'Online', ?, 1)";
    $stmt = $pdo->prepare($sql);
    $plan_name = ucfirst($plan) . " Membership";
    $stmt->execute([$user_id, $db_plan, $plan_name, $price, $start_date, $end_date, $txn_id]);

    log_activity($pdo, 'Membership Purchase', "Bought $plan_name", $user_id);

    // Get User Email
    $u = get_current_user_data($pdo);
    send_email($u['email'], "Membership Activated", "Your $plan_name is active until $end_date. TXN: $txn_id");

    success_response('Membership Activated!', ['redirect' => 'user/dashboard.php']);

} catch (Exception $e) {
    error_log($e->getMessage());
    error_response('Transaction Failed');
}
?>