<?php
require_once "db.php";

$message = "";
$msg_type = "";

// 1. ADD / INSERT STUDENT
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'add') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $course = trim($_POST['course'] ?? '');

    if (empty($name) || empty($email) || empty($course)) {
        $message = "All fields are required!";
        $msg_type = "danger";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format!";
        $msg_type = "danger";
    } else {
        $stmt = $conn->prepare("INSERT INTO students (name, email, course, status) VALUES (?, ?, ?, 'active')");
        $stmt->bind_param("sss", $name, $email, $course);
        if ($stmt->execute()) {
            $message = "Student added successfully!";
            $msg_type = "success";
        } else {
            $message = "Email already exists!";
            $msg_type = "danger";
        }
    }
}

// 2. UPDATE STUDENT
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = intval($_POST['student_id']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $course = trim($_POST['course']);

    $stmt = $conn->prepare("UPDATE students SET name = ?, email = ?, course = ? WHERE student_id = ?");
    $stmt->bind_param("sssi", $name, $email, $course, $id);
    if ($stmt->execute()) {
        $message = "Student details updated successfully!";
        $msg_type = "success";
    } else {
        $message = "Update failed: Email may already exist.";
        $msg_type = "danger";
    }
}

// 3. SOFT DELETE STUDENT
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("UPDATE students SET status = 'inactive' WHERE student_id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Student status changed to inactive (Soft Deleted)!";
        $msg_type = "warning";
    }
}

// 4. SEARCH & FILTER QUERY BUILDER
$search = trim($_GET['search'] ?? '');
$course_filter = trim($_GET['course'] ?? '');

$sql = "SELECT * FROM students WHERE 1=1";
$params = [];
$types = "";

if (!empty($search)) {
    $sql .= " AND (name LIKE ? OR email LIKE ?)";
    $search_param = "%" . $search . "%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

if (!empty($course_filter)) {
    $sql .= " AND course = ?";
    $params[] = $course_filter;
    $types .= "s";
}

$sql .= " ORDER BY student_id DESC";
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$students = $stmt->get_result();

// Edit state fetcher
$edit_row = null;
if (isset($_GET['edit_id'])) {
    $e_id = intval($_GET['edit_id']);
    $e_stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
    $e_stmt->bind_param("i", $e_id);
    $e_stmt->execute();
    $edit_row = $e_stmt->get_result()->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>StudentHub - Student Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Student Records Management</h3>
    <a href="export_csv.php" class="btn btn-success btn-sm">📥 Export to CSV</a>
  </div>

  <?php if (!empty($message)): ?>
    <div class="alert alert-<?= $msg_type ?> alert-dismissible fade show">
      <?= htmlspecialchars($message) ?>
    </div>
  <?php endif; ?>

  <!-- Add / Edit Form -->
  <div class="card mb-4 shadow-sm">
    <div class="card-header bg-primary text-white">
      <?= $edit_row ? 'Edit Student Details' : 'Add New Student' ?>
    </div>
    <div class="card-body">
      <form method="POST" action="students_crud.php" class="row g-3">
        <input type="hidden" name="action" value="<?= $edit_row ? 'edit' : 'add' ?>">
        <?php if ($edit_row): ?>
          <input type="hidden" name="student_id" value="<?= $edit_row['student_id'] ?>">
        <?php endif; ?>

        <div class="col-md-4">
          <label class="form-label">Full Name</label>
          <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($edit_row['name'] ?? '') ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Email Address</label>
          <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($edit_row['email'] ?? '') ?>">
        </div>
        <div class="col-md-3">
          <label class="form-label">Course</label>
          <select name="course" class="form-select" required>
            <option value="CSE" <?= (isset($edit_row) && $edit_row['course'] == 'CSE') ? 'selected' : '' ?>>CSE</option>
            <option value="IT" <?= (isset($edit_row) && $edit_row['course'] == 'IT') ? 'selected' : '' ?>>IT</option>
            <option value="CE" <?= (isset($edit_row) && $edit_row['course'] == 'CE') ? 'selected' : '' ?>>CE</option>
          </select>
        </div>
        <div class="col-md-1 d-flex align-items-end">
          <button type="submit" class="btn btn-primary w-100"><?= $edit_row ? 'Update' : 'Add' ?></button>
        </div>
      </form>
    </div>
  </div>

  <!-- Search & Filter Controls -->
  <div class="card mb-4 shadow-sm">
    <div class="card-body">
      <form method="GET" action="students_crud.php" class="row g-2">
        <div class="col-md-6">
          <input type="text" name="search" class="form-control" placeholder="Search by student name or email..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-md-3">
          <select name="course" class="form-select">
            <option value="">-- All Courses --</option>
            <option value="CSE" <?= ($course_filter === 'CSE') ? 'selected' : '' ?>>CSE</option>
            <option value="IT" <?= ($course_filter === 'IT') ? 'selected' : '' ?>>IT</option>
            <option value="CE" <?= ($course_filter === 'CE') ? 'selected' : '' ?>>CE</option>
          </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
          <button type="submit" class="btn btn-outline-secondary w-50">Filter</button>
          <a href="students_crud.php" class="btn btn-outline-dark w-50">Reset</a>
        </div>
      </form>
    </div>
  </div>

  <!-- Student Table -->
  <div class="card shadow-sm">
    <div class="card-body p-0">
      <table class="table table-bordered table-hover mb-0">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Course</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($students->num_rows > 0): ?>
            <?php while ($r = $students->fetch_assoc()): ?>
              <tr class="<?= $r['status'] === 'inactive' ? 'table-secondary text-muted' : '' ?>">
                <td><?= $r['student_id'] ?></td>
                <td><?= htmlspecialchars($r['name']) ?></td>
                <td><?= htmlspecialchars($r['email']) ?></td>
                <td><?= htmlspecialchars($r['course']) ?></td>
                <td>
                  <span class="badge <?= $r['status'] === 'active' ? 'bg-success' : 'bg-danger' ?>">
                    <?= ucfirst($r['status']) ?>
                  </span>
                </td>
                <td>
                  <a href="students_crud.php?edit_id=<?= $r['student_id'] ?>" class="btn btn-sm btn-info text-white">Edit</a>
                  <?php if ($r['status'] === 'active'): ?>
                    <a href="students_crud.php?delete=<?= $r['student_id'] ?>" 
                       onclick="return confirm('Are you sure you want to soft delete this student record?');" 
                       class="btn btn-sm btn-danger">Delete</a>
                  <?php else: ?>
                    <span class="badge bg-secondary">Deleted</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="6" class="text-center py-3">No records found matching criteria.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>