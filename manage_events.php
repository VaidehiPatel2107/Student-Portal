<?php
session_start();
require_once "db.php";

// 1. Role-based Access Restriction (Admin only)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?msg=unauthorized");
    exit();
}

$message = "";
$msg_type = "";
$upload_dir = "uploads/";

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Allowed image MIME types and maximum size (2MB)
$allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
$max_file_size = 2 * 1024 * 1024; // 2 MB in bytes

// 2. CREATE EVENT WITH POSTER UPLOAD
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'add') {
    $name  = trim($_POST['event_name'] ?? '');
    $date  = trim($_POST['event_date'] ?? '');
    $venue = trim($_POST['venue'] ?? '');

    if (empty($name) || empty($date) || empty($venue)) {
        $message = "All event fields are required!";
        $msg_type = "danger";
    } elseif (!isset($_FILES['poster']) || $_FILES['poster']['error'] === UPLOAD_ERR_NO_FILE) {
        $message = "Please select an event poster image.";
        $msg_type = "danger";
    } else {
        $file = $_FILES['poster'];
        $file_mime = mime_content_type($file['tmp_name']);

        if ($file['size'] > $max_file_size) {
            $message = "File size exceeds 2 MB limit! Upload aborted.";
            $msg_type = "danger";
        } elseif (!in_array($file_mime, $allowed_types)) {
            $message = "Invalid file type! Only JPG, PNG, and WEBP formats are permitted.";
            $msg_type = "danger";
        } else {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_filename = "poster_" . time() . "_" . uniqid() . "." . $ext;
            $destination = $upload_dir . $new_filename;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $stmt = $conn->prepare("INSERT INTO events (event_name, event_date, venue, poster) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $name, $date, $venue, $destination);
                if ($stmt->execute()) {
                    $message = "Event created and poster uploaded successfully!";
                    $msg_type = "success";
                } else {
                    $message = "Database error saving event.";
                    $msg_type = "danger";
                }
            } else {
                $message = "Server error while saving uploaded file.";
                $msg_type = "danger";
            }
        }
    }
}

// 3. UPDATE EVENT
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id    = intval($_POST['event_id']);
    $name  = trim($_POST['event_name']);
    $date  = trim($_POST['event_date']);
    $venue = trim($_POST['venue']);

    // Check if new poster uploaded
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['poster'];
        $file_mime = mime_content_type($file['tmp_name']);

        if ($file['size'] > $max_file_size) {
            $message = "File size exceeds 2 MB limit during update!";
            $msg_type = "danger";
        } elseif (!in_array($file_mime, $allowed_types)) {
            $message = "Invalid file type! Only JPG, PNG, and WEBP formats permitted.";
            $msg_type = "danger";
        } else {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_filename = "poster_" . time() . "_" . uniqid() . "." . $ext;
            $destination = $upload_dir . $new_filename;
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $stmt = $conn->prepare("UPDATE events SET event_name = ?, event_date = ?, venue = ?, poster = ? WHERE event_id = ?");
                $stmt->bind_param("ssssi", $name, $date, $venue, $destination, $id);
                $stmt->execute();
                $message = "Event and poster updated successfully!";
                $msg_type = "success";
            }
        }
    } else {
        $stmt = $conn->prepare("UPDATE events SET event_name = ?, event_date = ?, venue = ? WHERE event_id = ?");
        $stmt->bind_param("sssi", $name, $date, $venue, $id);
        $stmt->execute();
        $message = "Event details updated successfully!";
        $msg_type = "success";
    }
}

// 4. DELETE EVENT
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // Fetch poster file to delete from disk
    $f_stmt = $conn->prepare("SELECT poster FROM events WHERE event_id = ?");
    $f_stmt->bind_param("i", $id);
    $f_stmt->execute();
    $f_res = $f_stmt->get_result()->fetch_assoc();
    if ($f_res && !empty($f_res['poster']) && file_exists($f_res['poster'])) {
        unlink($f_res['poster']);
    }

    $d_stmt = $conn->prepare("DELETE FROM events WHERE event_id = ?");
    $d_stmt->bind_param("i", $id);
    if ($d_stmt->execute()) {
        $message = "Event deleted successfully!";
        $msg_type = "success";
    }
}

// Fetch events list
$events = $conn->query("SELECT * FROM events ORDER BY event_id DESC");

// Fetch edit state
$edit_event = null;
if (isset($_GET['edit_id'])) {
    $eid = intval($_GET['edit_id']);
    $e_stmt = $conn->prepare("SELECT * FROM events WHERE event_id = ?");
    $e_stmt->bind_param("i", $eid);
    $e_stmt->execute();
    $edit_event = $e_stmt->get_result()->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Event Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .poster-img { width: 70px; height: 70px; object-fit: cover; border-radius: 6px; }
  </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark px-4 py-2 d-flex justify-content-between">
  <span class="navbar-brand mb-0 h1">StudentHub Admin Console</span>
  <div>
    <span class="text-white me-3">Admin: <strong><?= htmlspecialchars($_SESSION['name'] ?? 'Admin') ?></strong></span>
    <a href="admin_dashboard.php" class="btn btn-outline-light btn-sm me-2">Dashboard</a>
    <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
  </div>
</nav>

<div class="container my-4">
  <h3 class="mb-3">Event Management & Poster Uploads</h3>

  <?php if (!empty($message)): ?>
    <div class="alert alert-<?= $msg_type ?> alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($message) ?>
    </div>
  <?php endif; ?>

  <!-- Event Form -->
  <div class="card mb-4 shadow-sm">
    <div class="card-header bg-primary text-white">
      <?= $edit_event ? 'Update Event Details' : 'Create New Event' ?>
    </div>
    <div class="card-body">
      <form action="manage_events.php" method="POST" enctype="multipart/form-data" class="row g-3">
        <input type="hidden" name="action" value="<?= $edit_event ? 'edit' : 'add' ?>">
        <?php if ($edit_event): ?>
          <input type="hidden" name="event_id" value="<?= $edit_event['event_id'] ?>">
        <?php endif; ?>

        <div class="col-md-4">
          <label class="form-label font-weight-bold">Event Name</label>
          <input type="text" name="event_name" class="form-control" required value="<?= htmlspecialchars($edit_event['event_name'] ?? '') ?>">
        </div>

        <div class="col-md-3">
          <label class="form-label font-weight-bold">Event Date</label>
          <input type="date" name="event_date" class="form-control" required value="<?= htmlspecialchars($edit_event['event_date'] ?? '') ?>">
        </div>

        <div class="col-md-3">
          <label class="form-label font-weight-bold">Venue</label>
          <input type="text" name="venue" class="form-control" required value="<?= htmlspecialchars($edit_event['venue'] ?? '') ?>">
        </div>

        <div class="col-md-2">
          <label class="form-label font-weight-bold">Event Poster</label>
          <input type="file" name="poster" class="form-control" accept="image/*" <?= $edit_event ? '' : 'required' ?>>
        </div>

        <div class="col-12 text-end">
          <?php if ($edit_event): ?>
            <a href="manage_events.php" class="btn btn-secondary me-2">Cancel</a>
          <?php endif; ?>
          <button type="submit" class="btn btn-primary"><?= $edit_event ? 'Update Event' : 'Create Event' ?></button>
        </div>
      </form>
    </div>
  </div>

  <!-- Events List Table -->
  <div class="card shadow-sm">
    <div class="card-header bg-dark text-white">Current Event Catalog</div>
    <div class="card-body p-0">
      <table class="table table-bordered table-hover align-middle mb-0">
        <thead class="table-secondary">
          <tr>
            <th>ID</th>
            <th>Poster</th>
            <th>Event Name</th>
            <th>Date</th>
            <th>Venue</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($events && $events->num_rows > 0): ?>
            <?php while ($ev = $events->fetch_assoc()): ?>
              <tr>
                <td><?= $ev['event_id'] ?></td>
                <td>
                  <?php if (!empty($ev['poster']) && file_exists($ev['poster'])): ?>
                    <img src="<?= htmlspecialchars($ev['poster']) ?>" alt="Poster" class="poster-img shadow-sm">
                  <?php else: ?>
                    <span class="badge bg-secondary">No Poster</span>
                  <?php endif; ?>
                </td>
                <td><strong><?= htmlspecialchars($ev['event_name']) ?></strong></td>
                <td><?= htmlspecialchars($ev['event_date']) ?></td>
                <td><?= htmlspecialchars($ev['venue']) ?></td>
                <td>
                  <a href="manage_events.php?edit_id=<?= $ev['event_id'] ?>" class="btn btn-info btn-sm text-white">Edit</a>
                  <a href="manage_events.php?delete=<?= $ev['event_id'] ?>" onclick="return confirm('Are you sure you want to delete this event?');" class="btn btn-danger btn-sm">Delete</a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="6" class="text-center py-3">No events found in database.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>