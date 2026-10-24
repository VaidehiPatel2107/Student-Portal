<?php
require_once "auth_middleware.php";

// Enforce that only logged in users can view this page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$currentUserRole = $_SESSION['role'];
$currentUsername = $_SESSION['username'];

// Aggregation Queries for Metrics
$studentCount = 0;
$userCount    = 0;
$eventCount   = 0;

if ($res = $conn->query("SELECT COUNT(*) AS total FROM api_students")) {
    $studentCount = $res->fetch_assoc()['total'];
}
if ($res = $conn->query("SELECT COUNT(*) AS total FROM users")) {
    $userCount = $res->fetch_assoc()['total'];
}
if ($res = $conn->query("SELECT COUNT(*) AS total FROM events")) {
    $eventCount = $res->fetch_assoc()['total'];
}

// Fetch 5 Latest Student Registrations
$recentRegistrations = [];
$regQuery = "SELECT id, name, email, course, created_at FROM api_students ORDER BY id DESC LIMIT 5";
if ($res = $conn->query($regQuery)) {
    while ($row = $res->fetch_assoc()) {
        $recentRegistrations[] = $row;
    }
}

// Log view action if Administrator
if ($currentUserRole === 'admin') {
    logAuditAction($conn, "Admin viewed analytics dashboard metrics");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>StudentHub - Management Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .stat-card { border-radius: 8px; border-left: 5px solid; }
        .stat-students { border-left-color: #0d6efd; }
        .stat-users { border-left-color: #198754; }
        .stat-events { border-left-color: #ffc107; }
    </style>
</head>
<body class="bg-light">

<!-- Dynamic Role-Based Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="dashboard.php">StudentHub</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link active" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="index.html">Student Directory</a></li>
                <!-- Dynamic RBAC Restricted Menu -->
                <?php if ($currentUserRole === 'admin'): ?>
                    <li class="nav-item"><a class="nav-link text-warning" href="admin_audit.php">Audit Logs</a></li>
                    <li class="nav-item"><a class="nav-link text-info" href="manage_users.php">User Management</a></li>
                <?php endif; ?>
            </ul>
            <div class="d-flex align-items-center">
                <span class="badge bg-secondary me-3">Role: <?= htmlspecialchars($currentUserRole) ?></span>
                <span class="text-white me-3"><?= htmlspecialchars($currentUsername) ?></span>
                <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
            </div>
        </div>
    </div>
</nav>

<div class="container my-5">
    <h3 class="fw-bold mb-4">Analytics & Overview</h3>

    <!-- Dynamic Metrics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card stat-card stat-students shadow-sm p-3 bg-white">
                <span class="text-muted fw-semibold">Total Students</span>
                <h2 class="display-6 fw-bold text-primary mb-0"><?= $studentCount ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card stat-users shadow-sm p-3 bg-white">
                <span class="text-muted fw-semibold">Registered Users</span>
                <h2 class="display-6 fw-bold text-success mb-0"><?= $userCount ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card stat-events shadow-sm p-3 bg-white">
                <span class="text-muted fw-semibold">Total Events</span>
                <h2 class="display-6 fw-bold text-warning mb-0"><?= $eventCount ?></h2>
            </div>
        </div>
    </div>

    <!-- Recent Registrations Feed Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-semibold">Latest Student Registrations</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Student Name</th>
                        <th>Email</th>
                        <th>Course</th>
                        <th>Registration Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recentRegistrations)): ?>
                        <?php foreach ($recentRegistrations as $reg): ?>
                            <tr>
                                <td><?= $reg['id'] ?></td>
                                <td><strong><?= htmlspecialchars($reg['name']) ?></strong></td>
                                <td><?= htmlspecialchars($reg['email']) ?></td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($reg['course']) ?></span></td>
                                <td><?= htmlspecialchars($reg['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-3 text-muted">No registrations found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>