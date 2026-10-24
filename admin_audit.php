<?php
require_once "auth_middleware.php";
checkAdminAccess(); // Triggers 403.php if role != 'admin'

$logs = [];
$res = $conn->query("SELECT * FROM audit_logs ORDER BY id DESC LIMIT 15");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $logs[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Audit Security Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Security Audit Log Feed</h3>
        <a href="dashboard.php" class="btn btn-outline-primary btn-sm">Back to Dashboard</a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-striped align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Log ID</th>
                        <th>User ID</th>
                        <th>Username</th>
                        <th>Action Performed</th>
                        <th>IP Address</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?= $log['id'] ?></td>
                            <td><?= $log['user_id'] ?></td>
                            <td><span class="badge bg-info text-dark"><?= htmlspecialchars($log['username']) ?></span></td>
                            <td><?= htmlspecialchars($log['action']) ?></td>
                            <td><code><?= htmlspecialchars($log['ip_address']) ?></code></td>
                            <td><?= htmlspecialchars($log['timestamp']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>