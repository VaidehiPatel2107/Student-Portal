<?php
require_once 'db.php';

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    $confirm_pass = $_POST['confirm_password'] ?? '';

    // 1. Empty field validation
    if (empty($user) || empty($email) || empty($pass) || empty($confirm_pass)) {
        $errors[] = "All fields are required.";
    }

    // 2. Invalid email validation
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please provide a valid email address.";
    }

    // 3. Password mismatch validation
    if (!empty($pass) && $pass !== $confirm_pass) {
        $errors[] = "Passwords do not match.";
    }

    // 4. Duplicate check & database insertion
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email LIMIT 1");
        $stmt->execute([':username' => $user, ':email' => $email]);

        if ($stmt->fetch()) {
            $errors[] = "Username or Email is already registered.";
        } else {
            // Password stored in hashed format
            $hashedPassword = password_hash($pass, PASSWORD_BCRYPT);

            // Prepared statement insertion
            $insertStmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
            $result = $insertStmt->execute([
                ':username' => $user,
                ':email' => $email,
                ':password' => $hashedPassword
            ]);

            if ($result) {
                $success = "Registration successful! You can now log in.";
            } else {
                $errors[] = "An error occurred during registration.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>StudentHub - Register</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { font-family: Arial, sans-serif; background-color: #f8f9fa; }
    .card { border-radius: 8px; border: 1px solid #dee2e6; }
  </style>
</head>
<body>
  <div class="container mt-5" style="max-width: 500px;">
    <div class="card shadow-sm p-4 bg-white">
      <h3 class="text-center mb-4">StudentHub Registration</h3>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
          <ul class="mb-0 ps-3">
            <?php foreach ($errors as $err): ?>
              <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>

      <form action="register.php" method="POST" novalidate>
        <div class="mb-3">
          <label class="form-label">Username</label>
          <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        </div>

        <div class="mb-3">
          <label class="form-label">Email Address</label>
          <input type="text" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>

        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control">
        </div>

        <div class="mb-3">
          <label class="form-label">Confirm Password</label>
          <input type="password" name="confirm_password" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary w-100">Register</button>
      </form>
    </div>
  </div>
</body>
</html>