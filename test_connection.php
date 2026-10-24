<?php
require_once "db.php";

try {
    // Prepared Statement to fetch records
    $sql = "SELECT student_id, name, email, course FROM students ORDER BY student_id ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $students = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Query Error: " . htmlspecialchars($e->getMessage()));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>StudentHub - Test Connection</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        .success-box {
            padding: 10px 15px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            display: inline-block;
            margin-bottom: 20px;
            border-radius: 4px;
            font-weight: bold;
        }
        table { border-collapse: collapse; width: 60%; }
        th, td { border: 1px solid #ddd; padding: 8px 12px; text-align: left; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #fafafa; }
    </style>
</head>
<body>

    <div class="success-box">Database Connected Successfully</div>

    <h2>Registered Students List</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Course</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['student_id']); ?></td>
                <td><?= htmlspecialchars($row['name']); ?></td>
                <td><?= htmlspecialchars($row['email']); ?></td>
                <td><?= htmlspecialchars($row['course']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>