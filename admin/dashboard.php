<?php
// admin/dashboard.php
require_once '../includes/functions.php';

// Security Check
if (!is_admin_logged_in()) {
    header("Location: login.html");
    exit;
}

$adminName = $_SESSION['admin_name'];

// 1. Fetch Stats
$stats = [
    'users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'requests' => $pdo->query("SELECT COUNT(*) FROM service_requests")->fetchColumn(),
    'pending' => $pdo->query("SELECT COUNT(*) FROM service_requests WHERE status = 'pending'")->fetchColumn(),
    'revenue' => $pdo->query("SELECT SUM(price) FROM memberships WHERE payment_status = 'completed'")->fetchColumn() ?: 0
];

// 2. Fetch Recent Requests
$stmt = $pdo->query("SELECT * FROM service_requests ORDER BY created_at DESC LIMIT 20");
$requests = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard -
        <?php echo SITE_NAME; ?>
    </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/png" href="../img/favicon.png">
    <style>
        .sidebar {
            min-height: 100vh;
            background: #2c3e50;
            color: white;
        }

        .sidebar a {
            color: #bdc3c7;
            text-decoration: none;
            display: block;
            padding: 10px 20px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #34495e;
            color: white;
        }

        .stat-card {
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>

<body>

    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar d-flex flex-column flex-shrink-0 p-3" style="width: 250px;">
            <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                <span class="fs-4 fw-bold">SK Admin</span>
            </a>
            <hr>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item"><a href="#" class="nav-link active"><i class="fas fa-tachometer-alt me-2"></i>
                        Dashboard</a></li>
                <li><a href="#" class="nav-link"><i class="fas fa-tools me-2"></i> Requests</a></li>
                <li><a href="#" class="nav-link"><i class="fas fa-users me-2"></i> Customers</a></li>
                <li><a href="#" class="nav-link"><i class="fas fa-user-cog me-2"></i> Technicians</a></li>
            </ul>
            <hr>
            <div class="dropdown">
                <a href="../backend/logout.php" class="d-flex align-items-center text-white text-decoration-none">
                    <i class="fas fa-sign-out-alt me-2"></i> <strong>Sign out</strong>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1 bg-light p-4">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Dashboard Overview</h2>
                <div>
                    <span class="text-muted me-2">Welcome,
                        <?php echo htmlspecialchars($adminName); ?>
                    </span>
                    <span class="badge bg-primary">Super Admin</span>
                </div>
            </div>

            <!-- Stats Row -->
            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="card stat-card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="text-muted text-uppercase small">Total Users</h6>
                            <h2 class="mb-0 text-primary">
                                <?php echo $stats['users']; ?>
                            </h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="text-muted text-uppercase small">Total Requests</h6>
                            <h2 class="mb-0 text-success">
                                <?php echo $stats['requests']; ?>
                            </h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="text-muted text-uppercase small">Pending Actions</h6>
                            <h2 class="mb-0 text-warning">
                                <?php echo $stats['pending']; ?>
                            </h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="text-muted text-uppercase small">Revenue (Memb.)</h6>
                            <h2 class="mb-0 text-info">₹
                                <?php echo number_format($stats['revenue']); ?>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Requests Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Recent Service Requests</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Service</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($requests)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">No requests found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($requests as $req): ?>
                                    <tr>
                                        <td>#
                                            <?php echo $req['request_id']; ?>
                                        </td>
                                        <td>
                                            <div class="fw-bold">
                                                <?php echo htmlspecialchars($req['name']); ?>
                                            </div>
                                            <div class="small text-muted">
                                                <?php echo htmlspecialchars($req['phone']); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($req['service_type']); ?>
                                        </td>
                                        <td>
                                            <?php echo date('d M', strtotime($req['preferred_date'])); ?>
                                        </td>
                                        <td>
                                            <?php
                                            $clr = 'secondary';
                                            if ($req['status'] == 'pending')
                                                $clr = 'warning';
                                            if ($req['status'] == 'confirmed')
                                                $clr = 'info';
                                            if ($req['status'] == 'completed')
                                                $clr = 'success';
                                            if ($req['status'] == 'cancelled')
                                                $clr = 'danger';
                                            ?>
                                            <span class="badge bg-<?php echo $clr; ?>">
                                                <?php echo ucfirst($req['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary"
                                                onclick="manageRequest(<?php echo $req['request_id']; ?>, '<?php echo $req['status']; ?>')">Manage</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Management Modal -->
    <div class="modal fade" id="modalManage" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Request #<span id="manageId"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formStatus">
                        <input type="hidden" name="request_id" id="inputReqId">
                        <div class="mb-3">
                            <label class="form-label">Update Status</label>
                            <select name="status" id="selectStatus" class="form-select">
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Update Status</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const modal = new bootstrap.Modal(document.getElementById('modalManage'));

        window.manageRequest = function (id, currentStatus) {
            document.getElementById('manageId').textContent = id;
            document.getElementById('inputReqId').value = id;
            document.getElementById('selectStatus').value = currentStatus;
            modal.show();
        };

        document.getElementById('formStatus').addEventListener('submit', async function (e) {
            e.preventDefault();
            const btn = this.querySelector('button');
            const originalText = btn.innerHTML;
            btn.innerHTML = 'Updating...';
            btn.disabled = true;

            try {
                const formData = new FormData(this);
                const response = await fetch('../backend/update-request-status.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    alert('✅ ' + result.message);
                    location.reload();
                } else {
                    alert('❌ ' + result.message);
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            } catch (err) {
                alert('Connection error');
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        });
    </script>
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

    <script src="../js/store.js"></script>
    <script src="../js/main.js"></script>
</body>


</html>