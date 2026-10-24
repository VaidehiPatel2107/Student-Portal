<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>StudentHub - Portal Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            /* Directly references login-bg.png in the same directory */
            background: url('login-bg.png') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
        }

        /* Glassmorphic card styling */
        .login-card {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 14px;
            box-shadow: 0 12px 30px rgba(13, 110, 253, 0.12), 0 4px 12px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.6);
            width: 100%;
            max-width: 410px;
        }

        .form-control, .form-select {
            background-color: rgba(255, 255, 255, 0.95);
            border: 1px solid #ced4da;
        }

        .form-control:focus, .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        }

        .btn-primary {
            background-color: #0d6efd;
            border: none;
            padding: 10px;
            font-weight: 600;
            border-radius: 6px;
            transition: all 0.2s ease-in-out;
        }

        .btn-primary:hover {
            background-color: #0b5ed7;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

<div class="card login-card p-4">
    <div class="card-body">
        <h3 class="fw-bold text-primary text-center mb-1">StudentHub</h3>
        <p class="text-muted small text-center mb-4">Enter credentials to access portal</p>
        
        <form action="switch.php" method="GET">
            <div class="mb-3">
                <label class="form-label fw-semibold">Username</label>
                <input type="text" name="user" class="form-control" value="vaidehi_admin" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Password</label>
                <input type="password" name="pass" class="form-control" value="••••••••" required>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Select Role</label>
                <select name="role" class="form-select">
                    <option value="admin">Administrator (vaidehi_admin)</option>
                    <option value="student">Student (rahul_student)</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">Sign In</button>
        </form>
    </div>
</div>

</body>
</html>