<?php
// user/dashboard.php
require_once '../includes/functions.php';

if (!is_logged_in()) {
    header("Location: ../login.html");
    exit;
}

$user = get_current_user_data($pdo);
$membership = get_membership_status($pdo, $_SESSION['user_id']);

// Get Requests
$reqStmt = $pdo->prepare("SELECT * FROM service_requests WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
$reqStmt->execute([$_SESSION['user_id']]);
$requests = $reqStmt->fetchAll();

// Stats
$totalReq = count($requests);
$activeReq = 0;
foreach ($requests as $r) {
    if (in_array($r['status'], ['pending', 'confirmed', 'assigned', 'in_progress']))
        $activeReq++;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Dashboard - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/png" href="../img/favicon.png">
</head>

<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.html"><?php echo SITE_NAME; ?></a>
            <div class="d-flex">
                <span class="text-white me-3 mt-2">Hello, <?php echo htmlspecialchars($user['name']); ?></span>
                <a href="../backend/logout.php" class="btn btn-outline-light btn-sm mt-1">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-primary">
                    <div class="card-body">
                        <h5 class="text-primary">Active Requests</h5>
                        <h2><?php echo $activeReq; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="text-muted">Total Requests</h5>
                        <h2><?php echo $totalReq; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm <?php echo $membership ? 'border-success' : 'border-warning'; ?>">
                    <div class="card-body">
                        <?php if ($membership): ?>
                            <h5 class="text-success">Active Membership</h5>
                            <p class="mb-0 fw-bold"><?php echo htmlspecialchars($membership['plan_name']); ?></p>
                            <small>Exp: <?php echo $membership['end_date']; ?></small>
                        <?php else: ?>
                            <h5>No Membership</h5>
                            <a href="../membership.html" class="btn btn-sm btn-warning">Get Membership</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="mb-4">
            <a href="../service-request.html" class="btn btn-primary me-2">+ New Request</a>
            <button class="btn btn-outline-secondary">Edit Profile</button>
        </div>

        <!-- History -->
        <div class="card shadow-sm">
            <div class="card-header bg-white font-weight-bold">Recent Service History</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Service</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($requests) > 0): ?>
                            <?php foreach ($requests as $req): ?>
                                <tr>
                                    <td>#<?php echo $req['request_id']; ?></td>
                                    <td><?php echo htmlspecialchars($req['service_type']); ?></td>
                                    <td><?php echo date('d M Y', strtotime($req['preferred_date'])); ?></td>
                                    <td>
                                        <?php
                                        $statusColor = 'secondary';
                                        if ($req['status'] == 'pending')
                                            $statusColor = 'warning';
                                        if ($req['status'] == 'confirmed')
                                            $statusColor = 'info';
                                        if ($req['status'] == 'completed')
                                            $statusColor = 'success';
                                        if ($req['status'] == 'cancelled')
                                            $statusColor = 'danger';
                                        ?>
                                        <span
                                            class="badge bg-<?php echo $statusColor; ?>"><?php echo ucfirst($req['status']); ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">No requests found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- FLOATING BUTTONS -->
    <div class="floating-container">
        <button id="themeToggle" class="float-btn btn-theme" title="Toggle Theme"><i class="fas fa-moon"></i></button>
        <a href="https://wa.me/9779767990237" target="_blank" class="float-btn btn-whatsapp" title="WhatsApp Us"><i
                class="fab fa-whatsapp"></i></a>
        <a href="tel:+9779767990237" class="float-btn btn-call" title="Call Now"><i class="fas fa-phone"></i></a>
        <button id="scrollToTop" class="float-btn btn-scroll-top" title="Scroll to Top"><i
                class="fas fa-arrow-up"></i></button>
        <button id="chatbotToggle" class="float-btn btn-primary" title="Chat with us"><i
                class="fas fa-comments"></i></button>
    </div>

    <!-- CHATBOT WIDGET -->
    <div id="chatbotWidget" class="chatbot-container">
        <div class="chatbot-header" id="chatbotHeader">
            <span><i class="fas fa-robot me-2"></i> Support Assistant</span>
            <i class="fas fa-times"></i>
        </div>
        <div class="chatbot-body" id="chatbotBody">
            <div class="bot-msg">Namaste! How can I help you today?</div>
            <!-- Chat log -->
        </div>
        <div class="chatbot-options p-2" id="chatbotOptions">
            <!-- Dynamic options -->
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/store.js"></script>
    <script src="../js/main.js"></script>
</body>


</html>