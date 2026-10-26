<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'db.php';

$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents('php://input'), true) ?? [];

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
        
        echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll()]);
        break;

    // -------------------------------------------------------------
    // CRUD: READ ALL ITEMS
    // -------------------------------------------------------------
    case 'read':
        $stmt = $pdo->query("SELECT * FROM items ORDER BY id DESC");
        echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll()]);
        break;

    // -------------------------------------------------------------
    // CRUD: CREATE NEW ITEM
    // -------------------------------------------------------------
    case 'create':
        if (!empty($input['name']) && !empty($input['category']) && isset($input['price'])) {
            $stmt = $pdo->prepare("INSERT INTO items (name, category, price) VALUES (?, ?, ?)");
            $stmt->execute([$input['name'], $input['category'], $input['price']]);
            echo json_encode(['status' => 'success', 'message' => 'Item created successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        }
        break;

    // -------------------------------------------------------------
    // CRUD: UPDATE EXISTING ITEM
    // -------------------------------------------------------------
    case 'update':
        if (!empty($input['id']) && !empty($input['name']) && !empty($input['category']) && isset($input['price'])) {
            $stmt = $pdo->prepare("UPDATE items SET name = ?, category = ?, price = ? WHERE id = ?");
            $stmt->execute([$input['name'], $input['category'], $input['price'], $input['id']]);
            echo json_encode(['status' => 'success', 'message' => 'Item updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid or missing data']);
        }
        break;

    // -------------------------------------------------------------
    // CRUD: DELETE ITEM
    // -------------------------------------------------------------
    case 'delete':
        if (!empty($input['id'])) {
            $stmt = $pdo->prepare("DELETE FROM items WHERE id = ?");
            $stmt->execute([$input['id']]);
            echo json_encode(['status' => 'success', 'message' => 'Item deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Item ID required']);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}
