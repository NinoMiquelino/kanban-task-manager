<?php
// api/boards.php
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
        getBoards();
        break;
    case 'POST':
        createBoard();
        break;
    case 'PUT':
        updateBoard();
        break;
    case 'DELETE':
        deleteBoard();
        break;
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método não permitido']);
}

function getBoards() {
    global $pdo;
    
    $user_id = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("
        SELECT b.*, 
               (SELECT COUNT(*) FROM tasks t 
                JOIN columns c ON t.column_id = c.id 
                WHERE c.board_id = b.id) as total_tasks
        FROM boards b 
        WHERE b.user_id = ? 
        ORDER BY b.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $boards = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'boards' => $boards]);
}

function createBoard() {
    global $pdo;
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $name = $data['name'] ?? '';
    $description = $data['description'] ?? '';
    $user_id = $_SESSION['user_id'];
    
    if (empty($name)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Nome do board é obrigatório']);
        return;
    }
    
    try {
        $pdo->beginTransaction();
        
        // Criar board
        $stmt = $pdo->prepare("INSERT INTO boards (name, description, user_id) VALUES (?, ?, ?)");
        $stmt->execute([$name, $description, $user_id]);
        $board_id = $pdo->lastInsertId();
        
        // Criar colunas padrão
        $default_columns = [
            ['To Do', 0],
            ['Doing', 1],
            ['Done', 2]
        ];
        
        foreach ($default_columns as $column) {
            $stmt = $pdo->prepare("INSERT INTO columns (name, board_id, position) VALUES (?, ?, ?)");
            $stmt->execute([$column[0], $board_id, $column[1]]);
        }
        
        $pdo->commit();
        
        echo json_encode(['success' => true, 'boardId' => $board_id]);
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erro ao criar board: ' . $e->getMessage()]);
    }
}

function updateBoard() {
    global $pdo;
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $board_id = $data['boardId'] ?? '';
    $name = $data['name'] ?? '';
    $description = $data['description'] ?? '';
    
    // Verificar se o board pertence ao usuário
    $stmt = $pdo->prepare("SELECT id FROM boards WHERE id = ? AND user_id = ?");
    $stmt->execute([$board_id, $_SESSION['user_id']]);
    
    if (!$stmt->fetch()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Board não encontrado']);
        return;
    }
    
    $stmt = $pdo->prepare("UPDATE boards SET name = ?, description = ? WHERE id = ?");
    
    try {
        $stmt->execute([$name, $description, $board_id]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erro ao atualizar board: ' . $e->getMessage()]);
    }
}

function deleteBoard() {
    global $pdo;
    
    $data = json_decode(file_get_contents('php://input'), true);
    $board_id = $data['boardId'] ?? '';
    
    // Verificar se o board pertence ao usuário
    $stmt = $pdo->prepare("SELECT id FROM boards WHERE id = ? AND user_id = ?");
    $stmt->execute([$board_id, $_SESSION['user_id']]);
    
    if (!$stmt->fetch()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Board não encontrado']);
        return;
    }
    
    // A exclusão em cascata cuidará das colunas e tarefas
    $stmt = $pdo->prepare("DELETE FROM boards WHERE id = ?");
    
    try {
        $stmt->execute([$board_id]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erro ao excluir board: ' . $e->getMessage()]);
    }
}
?>