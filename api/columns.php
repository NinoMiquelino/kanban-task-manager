<?php
// api/columns.php
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
        getColumns();
        break;
    case 'POST':
        createColumn();
        break;
    case 'PUT':
        updateColumn();
        break;
    case 'DELETE':
        deleteColumn();
        break;
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método não permitido']);
}

function getColumns() {
    global $pdo;
    
    $board_id = $_GET['board_id'] ?? '';
    
    if (empty($board_id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID do board é obrigatório']);
        return;
    }
    
    // Verificar se o board pertence ao usuário
    $stmt = $pdo->prepare("SELECT id FROM boards WHERE id = ? AND user_id = ?");
    $stmt->execute([$board_id, $_SESSION['user_id']]);
    
    if (!$stmt->fetch()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Board não encontrado']);
        return;
    }
    
    $stmt = $pdo->prepare("
        SELECT c.*, 
               (SELECT COUNT(*) FROM tasks t WHERE t.column_id = c.id) as task_count
        FROM columns c 
        WHERE c.board_id = ? 
        ORDER BY c.position
    ");
    $stmt->execute([$board_id]);
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'columns' => $columns]);
}

function createColumn() {
    global $pdo;
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $name = $data['name'] ?? '';
    $board_id = $data['board_id'] ?? '';
    
    if (empty($name) || empty($board_id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Nome e board são obrigatórios']);
        return;
    }
    
    // Verificar se o board pertence ao usuário
    $stmt = $pdo->prepare("SELECT id FROM boards WHERE id = ? AND user_id = ?");
    $stmt->execute([$board_id, $_SESSION['user_id']]);
    
    if (!$stmt->fetch()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Board não encontrado']);
        return;
    }
    
    // Encontrar a próxima posição
    $stmt = $pdo->prepare("SELECT COALESCE(MAX(position), 0) + 1 as next_position FROM columns WHERE board_id = ?");
    $stmt->execute([$board_id]);
    $next_position = $stmt->fetchColumn();
    
    $stmt = $pdo->prepare("INSERT INTO columns (name, board_id, position) VALUES (?, ?, ?)");
    
    try {
        $stmt->execute([$name, $board_id, $next_position]);
        echo json_encode(['success' => true, 'columnId' => $pdo->lastInsertId()]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erro ao criar coluna: ' . $e->getMessage()]);
    }
}

function updateColumn() {
    global $pdo;
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $column_id = $data['columnId'] ?? '';
    $name = $data['name'] ?? '';
    
    // Verificar se a coluna pertence ao usuário
    $stmt = $pdo->prepare("
        SELECT c.id FROM columns c 
        JOIN boards b ON c.board_id = b.id 
        WHERE c.id = ? AND b.user_id = ?
    ");
    $stmt->execute([$column_id, $_SESSION['user_id']]);
    
    if (!$stmt->fetch()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Coluna não encontrada']);
        return;
    }
    
    $stmt = $pdo->prepare("UPDATE columns SET name = ? WHERE id = ?");
    
    try {
        $stmt->execute([$name, $column_id]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erro ao atualizar coluna: ' . $e->getMessage()]);
    }
}

function deleteColumn() {
    global $pdo;
    
    $data = json_decode(file_get_contents('php://input'), true);
    $column_id = $data['columnId'] ?? '';
    
    // Verificar se a coluna pertence ao usuário
    $stmt = $pdo->prepare("
        SELECT c.id FROM columns c 
        JOIN boards b ON c.board_id = b.id 
        WHERE c.id = ? AND b.user_id = ?
    ");
    $stmt->execute([$column_id, $_SESSION['user_id']]);
    
    if (!$stmt->fetch()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Coluna não encontrada']);
        return;
    }
    
    // A exclusão em cascata cuidará das tarefas
    $stmt = $pdo->prepare("DELETE FROM columns WHERE id = ?");
    
    try {
        $stmt->execute([$column_id]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erro ao excluir coluna: ' . $e->getMessage()]);
    }
}
?>