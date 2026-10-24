<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$conn = new mysqli("localhost", "root", "", "studenthub");
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["status" => false, "message" => "Database error: " . $conn->connect_error]);
    exit();
}
$conn->set_charset("utf8mb4");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    // 1. GET ALL OR SINGLE
    case 'GET':
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $stmt = $conn->prepare("SELECT * FROM api_students WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($row = $res->fetch_assoc()) {
                echo json_encode(["status" => true, "data" => $row]);
            } else {
                http_response_code(404);
                echo json_encode(["status" => false, "message" => "Student not found."]);
            }
        } else {
            $res = $conn->query("SELECT * FROM api_students ORDER BY id DESC");
            $students = [];
            while ($row = $res->fetch_assoc()) {
                $students[] = $row;
            }
            echo json_encode(["status" => true, "data" => $students]);
        }
        break;

    // 2. CREATE (POST)
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $course = trim($data['course'] ?? '');

        if (empty($name) || empty($email) || empty($course)) {
            http_response_code(400);
            echo json_encode(["status" => false, "message" => "All fields are required."]);
            exit();
        }

        $stmt = $conn->prepare("INSERT INTO api_students (name, email, course) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $course);
        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(["status" => true, "message" => "Student created successfully.", "id" => $conn->insert_id]);
        } else {
            http_response_code(409);
            echo json_encode(["status" => false, "message" => "Email already registered."]);
        }
        break;

    // 3. UPDATE (PUT)
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $id = intval($data['id'] ?? 0);
        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $course = trim($data['course'] ?? '');

        if ($id <= 0 || empty($name) || empty($email) || empty($course)) {
            http_response_code(400);
            echo json_encode(["status" => false, "message" => "Valid ID and fields required."]);
            exit();
        }

        $stmt = $conn->prepare("UPDATE api_students SET name = ?, email = ?, course = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $email, $course, $id);
        if ($stmt->execute()) {
            echo json_encode(["status" => true, "message" => "Student record updated successfully."]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => false, "message" => "Failed to update record."]);
        }
        break;

    // 4. DELETE
    case 'DELETE':
        $id = intval($_GET['id'] ?? 0);
        if ($id <= 0) {
            $data = json_decode(file_get_contents("php://input"), true);
            $id = intval($data['id'] ?? 0);
        }

        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(["status" => false, "message" => "Valid ID required."]);
            exit();
        }

        $stmt = $conn->prepare("DELETE FROM api_students WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(["status" => true, "message" => "Student deleted successfully."]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => false, "message" => "Failed to delete student."]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["status" => false, "message" => "Method not allowed."]);
        break;
}
?>