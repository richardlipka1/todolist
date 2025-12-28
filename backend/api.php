<?php
require_once 'config.php';

$conn = getDBConnection();
$method = $_SERVER['REQUEST_METHOD'];
$path = isset($_GET['path']) ? $_GET['path'] : '';

// Generate a random hash for todolist ID using cryptographically secure method
function generateHash() {
    try {
        // Use random_bytes for better entropy and collision resistance
        return bin2hex(random_bytes(16));
    } catch (Exception $e) {
        // Fallback to less secure but reliable method if random_bytes fails
        return bin2hex(openssl_random_pseudo_bytes(16));
    }
}

// Sanitize input string
function sanitizeInput($input, $maxLength = 255) {
    if (!is_string($input)) {
        return '';
    }
    $input = trim($input);
    $input = substr($input, 0, $maxLength);
    return $input;
}

// Validate hash ID format (32-character hexadecimal string from random_bytes)
function isValidHash($hash) {
    return is_string($hash) && preg_match('/^[a-f0-9]{32}$/i', $hash);
}

// Handle different API endpoints
switch ($method) {
    case 'POST':
        if ($path === 'create') {
            // Create new todolist
            $data = json_decode(file_get_contents('php://input'), true);
            $title = isset($data['title']) ? sanitizeInput($data['title'], 255) : 'New Todo List';
            
            if (empty($title)) {
                $title = 'New Todo List';
            }
            
            $hash = generateHash();
            
            $stmt = $conn->prepare("INSERT INTO todolists (hash_id, title) VALUES (?, ?)");
            $stmt->bind_param("ss", $hash, $title);
            
            if ($stmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'hash_id' => $hash,
                    'title' => $title
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to create todolist']);
            }
            $stmt->close();
            
        } elseif ($path === 'add-item') {
            // Add item to todolist
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['hash_id']) || !isset($data['task'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required fields']);
                break;
            }
            
            $hash = $data['hash_id'];
            $task = sanitizeInput($data['task'], 1000);
            
            if (!isValidHash($hash)) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid hash ID']);
                break;
            }
            
            if (empty($task)) {
                http_response_code(400);
                echo json_encode(['error' => 'Task cannot be empty']);
                break;
            }
            
            $stmt = $conn->prepare("INSERT INTO todo_items (todolist_hash, task) VALUES (?, ?)");
            $stmt->bind_param("ss", $hash, $task);
            
            if ($stmt->execute()) {
                $item_id = $conn->insert_id;
                echo json_encode([
                    'success' => true,
                    'item_id' => $item_id,
                    'task' => $task,
                    'is_done' => false
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to add item']);
            }
            $stmt->close();
            
        } elseif ($path === 'toggle-item') {
            // Toggle item done status
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['item_id']) || !isset($data['is_done'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required fields']);
                break;
            }
            
            $item_id = filter_var($data['item_id'], FILTER_VALIDATE_INT);
            $is_done = $data['is_done'] ? 1 : 0;
            
            if ($item_id === false || $item_id < 1) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid item ID']);
                break;
            }
            
            $stmt = $conn->prepare("UPDATE todo_items SET is_done = ? WHERE id = ?");
            $stmt->bind_param("ii", $is_done, $item_id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to update item']);
            }
            $stmt->close();
            
        } elseif ($path === 'update-title') {
            // Update todolist title
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['hash_id']) || !isset($data['title'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required fields']);
                break;
            }
            
            $hash = $data['hash_id'];
            $title = sanitizeInput($data['title'], 255);
            
            if (!isValidHash($hash)) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid hash ID']);
                break;
            }
            
            if (empty($title)) {
                http_response_code(400);
                echo json_encode(['error' => 'Title cannot be empty']);
                break;
            }
            
            $stmt = $conn->prepare("UPDATE todolists SET title = ? WHERE hash_id = ?");
            $stmt->bind_param("ss", $title, $hash);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to update title']);
            }
            $stmt->close();
        }
        break;
        
    case 'GET':
        if (preg_match('/^todolist\/(.+)$/', $path, $matches)) {
            // Get todolist and its items
            $hash = $matches[1];
            
            if (!isValidHash($hash)) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid hash ID']);
                break;
            }
            
            // Get todolist info
            $stmt = $conn->prepare("SELECT * FROM todolists WHERE hash_id = ?");
            $stmt->bind_param("s", $hash);
            $stmt->execute();
            $result = $stmt->get_result();
            $todolist = $result->fetch_assoc();
            $stmt->close();
            
            if (!$todolist) {
                http_response_code(404);
                echo json_encode(['error' => 'Todolist not found']);
                break;
            }
            
            // Get items
            $stmt = $conn->prepare("SELECT * FROM todo_items WHERE todolist_hash = ? ORDER BY created_at ASC");
            $stmt->bind_param("s", $hash);
            $stmt->execute();
            $result = $stmt->get_result();
            $items = [];
            while ($row = $result->fetch_assoc()) {
                $row['is_done'] = (bool)$row['is_done'];
                $items[] = $row;
            }
            $stmt->close();
            
            echo json_encode([
                'todolist' => $todolist,
                'items' => $items
            ]);
        } elseif ($path === 'todolists') {
            // Get all todolists
            $result = $conn->query("SELECT * FROM todolists ORDER BY created_at DESC");
            $todolists = [];
            while ($row = $result->fetch_assoc()) {
                $todolists[] = $row;
            }
            echo json_encode(['todolists' => $todolists]);
        }
        break;
        
    case 'DELETE':
        if (preg_match('/^item\/(\d+)$/', $path, $matches)) {
            // Delete item
            $item_id = filter_var($matches[1], FILTER_VALIDATE_INT);
            
            if ($item_id === false || $item_id < 1) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid item ID']);
                break;
            }
            
            $stmt = $conn->prepare("DELETE FROM todo_items WHERE id = ?");
            $stmt->bind_param("i", $item_id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to delete item']);
            }
            $stmt->close();
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}

$conn->close();
?>
