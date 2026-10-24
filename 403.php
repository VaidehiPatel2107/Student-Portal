<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>403 Forbidden - Access Restricted</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .error-card { max-width: 550px; margin-top: 100px; border-radius: 10px; }
    </style>
</head>
<body>
<div class="container d-flex justify-content-center">
    <div class="card error-card shadow p-4 text-center border-danger">
        <h1 class="display-3 text-danger fw-bold">403</h1>
        <h3 class="fw-semibold">Access Forbidden</h3>
        <p class="text-muted mt-2">
            You do not have administrative clearance to access this resource. Your access attempt has been logged for security.
        </p>
        <div class="mt-3">
            <a href="dashboard.php" class="btn btn-outline-secondary me-2">Return Home</a>
            <a href="logout.php" class="btn btn-danger">Switch Account</a>
        </div>
    </div>
</div>
</body>
</html>