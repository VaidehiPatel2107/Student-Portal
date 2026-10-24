<?php
require_once "db.php";

// Set headers for download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=students_record_' . date('Ymd_His') . '.csv');

$output = fopen('php://output', 'w');

// CSV Column Headers
fputcsv($output, ['Student ID', 'Name', 'Email', 'Course', 'Status']);

// Query only the valid existing columns in the students table
$query = "SELECT student_id, name, email, course, status FROM students ORDER BY student_id ASC";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['student_id'],
        $row['name'],
        $row['email'],
        $row['course'],
        $row['status']
    ]);
}

fclose($output);
exit();
?>