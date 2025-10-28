<?php
// api/tasks.php
include '../includes/config.php';

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit;
}

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        getTasks();
        break;
    case 'POST':
        createTask();
        break;
    case 'PUT':
        updateTask();
        break;
    case 'DELETE':
        deleteTask();
        break;
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método não permitido']);
}

function createTask() {
    global $pdo;
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $title = $data['title'] ?? '';
    $description = $data['description'] ?? '';
    $column_id = $data['column_id'] ?? '';
    $priority = $data['priority'] ?? 'medium';
    $due_date = $data['due_date'] ?? null;
    
    // Validar se a coluna pertence ao usuário
    $stmt = $pdo->prepare("
        SELECT c.id FROM columns c 
        JOIN boards b ON c.board_id = b.id 
        WHERE c.id = ? AND b.user_id = ?
    ");
    $stmt->execute([$column_id, $_SESSION['user_id']]);
    $column = $stmt->fetch();
    
    if (!$column) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Coluna não encontrada']);
        return;
    }
    
    // Inserir tarefa
    $stmt = $pdo->prepare("
        INSERT INTO tasks (title, description, column_id, priority, due_date, position) 
        VALUES (?, ?, ?, ?, ?, (SELECT COALESCE(MAX(position), 0) + 1 FROM tasks WHERE column_id = ?))
    ");
    
    try {
        $stmt->execute([$title, $description, $column_id, $priority, $due_date, $column_id]);
        echo json_encode(['success' => true, 'taskId' => $pdo->lastInsertId()]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erro ao criar tarefa: ' . $e->getMessage()]);
    }
}

function updateTask() {
    global $pdo;
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $task_id = $data['taskId'] ?? '';
    $column_id = $data['columnId'] ?? '';
    
    // Validar se a tarefa e coluna pertencem ao usuário
    $stmt = $pdo->prepare("
        SELECT t.id FROM tasks t 
        JOIN columns c ON t.column_id = c.id 
        JOIN boards b ON c.board_id = b.id 
        WHERE t.id = ? AND b.user_id = ?
    ");
    $stmt->execute([$task_id, $_SESSION['user_id']]);
    $task = $stmt->fetch();
    
    if (!$task) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Tarefa não encontrada']);
        return;
    }
    
    $stmt = $pdo->prepare("
        SELECT c.id FROM columns c 
        JOIN boards b ON c.board_id = b.id 
        WHERE c.id = ? AND b.user_id = ?
    ");
    $stmt->execute([$column_id, $_SESSION['user_id']]);
    $column = $stmt->fetch();
    
    if (!$column) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Coluna não encontrada']);
        return;
    }
    
    // Atualizar coluna da tarefa
    $stmt = $pdo->prepare("UPDATE tasks SET column_id = ? WHERE id = ?");
    
    try {
        $stmt->execute([$column_id, $task_id]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erro ao atualizar tarefa: ' . $e->getMessage()]);
    }
}

function deleteTask() {
    global $pdo;
    
    $data = json_decode(file_get_contents('php://input'), true);
    $task_id = $data['taskId'] ?? '';
    
    // Validar se a tarefa pertence ao usuário
    $stmt = $pdo->prepare("
        SELECT t.id FROM tasks t 
        JOIN columns c ON t.column_id = c.id 
        JOIN boards b ON c.board_id = b.id 
        WHERE t.id = ? AND b.user_id = ?
    ");
    $stmt->execute([$task_id, $_SESSION['user_id']]);
    $task = $stmt->fetch();
    
    if (!$task) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Tarefa não encontrada']);
        return;
    }
    
    // Excluir tarefa
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
    
    try {
        $stmt->execute([$task_id]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erro ao excluir tarefa: ' . $e->getMessage()]);
    }
}
?>