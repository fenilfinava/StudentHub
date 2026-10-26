<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'db.php';

$action = $_GET['action'] ?? '';
$type   = $_GET['type'] ?? 'students'; // default to StudentHub students
$input  = json_decode(file_get_contents('php://input'), true) ?? [];

switch ($action) {
    // -------------------------------------------------------------
    // DYNAMIC MENU WITH ROLE-BASED ACCESS CONTROL (RBAC)
    // -------------------------------------------------------------
    case 'get_menu':
        $role = $_GET['role'] ?? 'user';

        $rolesAllowed = ['user'];
        if ($role === 'editor') {
            $rolesAllowed = ['user', 'editor'];
        } elseif ($role === 'admin') {
            $rolesAllowed = ['user', 'editor', 'admin'];
        }

        $placeholders = implode(',', array_fill(0, count($rolesAllowed), '?'));
        $stmt = $pdo->prepare("SELECT title, link FROM menu_items WHERE min_role IN ($placeholders)");
        $stmt->execute($rolesAllowed);

        echo json_encode([
            'status' => 'success',
            'role'   => $role,
            'data'   => $stmt->fetchAll()
        ]);
        break;

    // -------------------------------------------------------------
    // CRUD: READ ALL RECORDS (Students or Items)
    // -------------------------------------------------------------
    case 'read':
        if ($type === 'items') {
            $stmt = $pdo->query("SELECT * FROM items ORDER BY id DESC");
        } else {
            $stmt = $pdo->query("SELECT id, name, email, course, gender, status, created_at FROM students ORDER BY id DESC");
        }
        echo json_encode([
            'status' => 'success',
            'type'   => $type,
            'data'   => $stmt->fetchAll()
        ]);
        break;

    // -------------------------------------------------------------
    // CRUD: CREATE NEW RECORD
    // -------------------------------------------------------------
    case 'create':
        if ($type === 'items' || (isset($input['price']) && !isset($input['email']))) {
            if (!empty($input['name']) && !empty($input['category']) && isset($input['price'])) {
                $stmt = $pdo->prepare("INSERT INTO items (name, category, price) VALUES (?, ?, ?)");
                $stmt->execute([$input['name'], $input['category'], $input['price']]);
                echo json_encode(['status' => 'success', 'message' => 'Campus resource item created successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Missing required fields for item']);
            }
        } else {
            // StudentHub Student Creation
            if (!empty($input['name']) && !empty($input['email']) && !empty($input['course']) && !empty($input['gender'])) {
                $stmt = $pdo->prepare("INSERT INTO students (name, email, course, gender, status) VALUES (?, ?, ?, ?, 'Active')");
                $stmt->execute([$input['name'], $input['email'], $input['course'], $input['gender']]);
                echo json_encode(['status' => 'success', 'message' => 'Student record created successfully!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Missing required student fields (Name, Email, Course, Gender)']);
            }
        }
        break;

    // -------------------------------------------------------------
    // CRUD: UPDATE EXISTING RECORD
    // -------------------------------------------------------------
    case 'update':
        if ($type === 'items' || (isset($input['price']) && !isset($input['email']))) {
            if (!empty($input['id']) && !empty($input['name']) && !empty($input['category']) && isset($input['price'])) {
                $stmt = $pdo->prepare("UPDATE items SET name = ?, category = ?, price = ? WHERE id = ?");
                $stmt->execute([$input['name'], $input['category'], $input['price'], $input['id']]);
                echo json_encode(['status' => 'success', 'message' => 'Item updated successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid or missing data for update']);
            }
        } else {
            // StudentHub Student Update
            if (!empty($input['id']) && !empty($input['name']) && !empty($input['email']) && !empty($input['course']) && !empty($input['gender'])) {
                $stmt = $pdo->prepare("UPDATE students SET name = ?, email = ?, course = ?, gender = ? WHERE id = ?");
                $stmt->execute([$input['name'], $input['email'], $input['course'], $input['gender'], $input['id']]);
                echo json_encode(['status' => 'success', 'message' => 'Student record updated successfully!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid or missing data for student update']);
            }
        }
        break;

    // -------------------------------------------------------------
    // CRUD: DELETE RECORD
    // -------------------------------------------------------------
    case 'delete':
        if (!empty($input['id'])) {
            if ($type === 'items') {
                $stmt = $pdo->prepare("DELETE FROM items WHERE id = ?");
            } else {
                $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
            }
            $stmt->execute([$input['id']]);
            echo json_encode([
                'status'  => 'success',
                'message' => ($type === 'items' ? 'Item' : 'Student record') . ' deleted successfully!'
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Record ID is required for deletion']);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}
