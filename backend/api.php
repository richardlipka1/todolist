<?php
require_once 'config.php';

$conn = getDBConnection();
$method = $_SERVER['REQUEST_METHOD'];
$path = isset($_GET['path']) ? $_GET['path'] : '';

// Generate a random hash for todolist ID
function generateHash() {
    return md5(uniqid(rand(), true));
}

// Handle different API endpoints
switch ($method) {
    case 'POST':
        if ($path === 'create') {
            // Create new todolist
            $data = json_decode(file_get_contents('php://input'), true);
            $title = isset($data['title']) ? $data['title'] : 'New Todo List';
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
            $hash = $data['hash_id'];
            $task = $data['task'];
            
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
            $item_id = $data['item_id'];
            $is_done = $data['is_done'] ? 1 : 0;
            
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
            $hash = $data['hash_id'];
            $title = $data['title'];
            
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
                exit();
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
            $item_id = $matches[1];
            
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
