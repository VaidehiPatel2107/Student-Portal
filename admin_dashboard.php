<?php
session_start();
require_once "timeout.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?msg=unauthorized");
    exit();
}
if ($_SESSION['role'] !== 'admin') {
    header("Location: student_dashboard.php?error=unauthorized");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>StudentHub - Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-5" style="max-width: 650px;">
    <div class="card shadow-sm p-4 bg-white">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Admin Dashboard</h3>
        <span class="badge bg-primary">Role: ADMIN</span>
      </div>

      <p class="lead">Welcome, Administrator <strong><?= htmlspecialchars($_SESSION['name']); ?></strong>!</p>

      <?php if (isset($_GET['error']) && $_GET['error'] === 'unauthorized'): ?>
        <div class="alert alert-danger py-2">Access Denied: Admin cannot view Student Dashboard!</div>
      <?php endif; ?>

      <div class="border rounded p-3 bg-light mb-3">
        <h6 class="text-primary mb-2">Session Audit & Security Status:</h6>
        <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($_SESSION['email']); ?></p>
        <p class="mb-1"><strong>Active Session ID:</strong> <code class="text-dark"><?= session_id(); ?></code></p>
        <?php if (isset($_SESSION['old_session_id'])): ?>
          <p class="mb-1"><strong>Previous Session ID:</strong> <code><?= htmlspecialchars($_SESSION['old_session_id']); ?></code></p>
          <p class="text-success mb-0 fw-bold">✔ Session Fixation Protected (ID Regenerated on Login)</p>
        <?php endif; ?>
        <p class="text-muted small mt-2 mb-0">Inactivity Timeout: 60 Seconds</p>
      </div>

      <div class="d-flex justify-content-between align-items-center">
        <a href="student_dashboard.php" class="text-danger small">Test: Attempt to open Student Dashboard</a>
        <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
      </div>
    </div>
  </div>
</body>
</html>