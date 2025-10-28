<?php
// board.php
include 'includes/config.php';
include 'includes/auth.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$board_id = $_GET['id'] ?? null;
if (!$board_id) {
    header('Location: index.php');
    exit;
}

// Buscar board
$stmt = $pdo->prepare("SELECT * FROM boards WHERE id = ? AND user_id = ?");
$stmt->execute([$board_id, $_SESSION['user_id']]);
$board = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$board) {
    header('Location: index.php');
    exit;
}

// Buscar colunas e tarefas
$stmt = $pdo->prepare("
    SELECT c.*, 
           (SELECT COUNT(*) FROM tasks t WHERE t.column_id = c.id) as task_count
    FROM columns c 
    WHERE c.board_id = ? 
    ORDER BY c.position
");
$stmt->execute([$board_id]);
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar todas as tarefas
$stmt = $pdo->prepare("
    SELECT t.*, c.name as column_name 
    FROM tasks t 
    JOIN columns c ON t.column_id = c.id 
    WHERE c.board_id = ? 
    ORDER BY t.position
");
$stmt->execute([$board_id]);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organizar tarefas por coluna
$tasksByColumn = [];
foreach ($tasks as $task) {
    $tasksByColumn[$task['column_id']][] = $task;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($board['name']); ?> - Kanban Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-6">
        <!-- Board Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800"><?php echo htmlspecialchars($board['name']); ?></h1>
                <?php if ($board['description']): ?>
                    <p class="text-gray-600 mt-1"><?php echo htmlspecialchars($board['description']); ?></p>
                <?php endif; ?>
            </div>
            <button onclick="openCreateTaskModal()" 
                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
                <i class="fas fa-plus"></i>
                <span>Nova Tarefa</span>
            </button>
        </div>

        <!-- Kanban Board -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6" id="kanban-board">
            <?php foreach ($columns as $column): ?>
                <div class="bg-gray-100 rounded-lg p-4" data-column-id="<?php echo $column['id']; ?>">
                    <!-- Column Header -->
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold text-gray-800 text-lg">
                            <?php echo htmlspecialchars($column['name']); ?>
                        </h3>
                        <span class="bg-blue-500 text-white text-sm px-2 py-1 rounded-full">
                            <?php echo $column['task_count']; ?>
                        </span>
                    </div>

                    <!-- Tasks Container -->
                    <div class="tasks-container space-y-3 min-h-32" 
                         ondrop="drop(event)" 
                         ondragover="allowDrop(event)">
                        <?php if (isset($tasksByColumn[$column['id']])): ?>
                            <?php foreach ($tasksByColumn[$column['id']] as $task): ?>
                                <div class="task bg-white rounded-lg shadow-sm p-4 cursor-move border-l-4 
                                            <?php echo getPriorityColor($task['priority']); ?>"
                                     draggable="true"
                                     ondragstart="dragStart(event)"
                                     data-task-id="<?php echo $task['id']; ?>">
                                    
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="font-semibold text-gray-800"><?php echo htmlspecialchars($task['title']); ?></h4>
                                        <div class="flex space-x-1">
                                            <button onclick="editTask(<?php echo $task['id']; ?>)" 
                                                    class="text-gray-400 hover:text-blue-500">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="deleteTask(<?php echo $task['id']; ?>)" 
                                                    class="text-gray-400 hover:text-red-500">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <?php if ($task['description']): ?>
                                        <p class="text-gray-600 text-sm mb-3"><?php echo htmlspecialchars($task['description']); ?></p>
                                    <?php endif; ?>

                                    <div class="flex justify-between items-center text-xs text-gray-500">
                                        <?php if ($task['due_date']): ?>
                                            <span>
                                                <i class="fas fa-calendar mr-1"></i>
                                                <?php echo date('d/m/Y', strtotime($task['due_date'])); ?>
                                            </span>
                                        <?php endif; ?>
                                        <span class="capitalize <?php echo getPriorityTextColor($task['priority']); ?>">
                                            <?php echo $task['priority']; ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center text-gray-400 py-8 border-2 border-dashed border-gray-300 rounded-lg">
                                <i class="fas fa-tasks text-2xl mb-2"></i>
                                <p>Nenhuma tarefa</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Add Task Button -->
                    <button onclick="openCreateTaskModal(<?php echo $column['id']; ?>)" 
                            class="w-full mt-3 py-2 text-gray-500 hover:text-gray-700 hover:bg-gray-200 rounded-lg transition-colors">
                        <i class="fas fa-plus mr-2"></i>Adicionar Tarefa
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- Create Task Modal -->
    <div id="createTaskModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-96 max-h-[90vh] overflow-y-auto">
            <h3 class="text-xl font-semibold mb-4">Nova Tarefa</h3>
            <form id="createTaskForm">
                <input type="hidden" name="column_id" id="taskColumnId">
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Título</label>
                    <input type="text" name="title" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Descrição</label>
                    <textarea name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Prioridade</label>
                        <select name="priority" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option value="low">Baixa</option>
                            <option value="medium" selected>Média</option>
                            <option value="high">Alta</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Data de Vencimento</label>
                        <input type="date" name="due_date"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeCreateTaskModal()" 
                            class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancelar</button>
                    <button type="submit" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Criar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="js/drag-drop.js"></script>
    <script src="js/api.js"></script>
    <script>
        let currentColumnId = null;

        function openCreateTaskModal(columnId = null) {
            currentColumnId = columnId;
            document.getElementById('taskColumnId').value = columnId;
            document.getElementById('createTaskModal').classList.remove('hidden');
            document.getElementById('createTaskModal').classList.add('flex');
        }

        function closeCreateTaskModal() {
            document.getElementById('createTaskModal').classList.add('hidden');
            document.getElementById('createTaskModal').classList.remove('flex');
            document.getElementById('createTaskForm').reset();
        }

        // Form submission
        document.getElementById('createTaskForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData);
            
            try {
                const response = await api.post('/api/tasks.php', data);
                if (response.success) {
                    window.location.reload();
                }
            } catch (error) {
                console.error('Erro ao criar tarefa:', error);
                alert('Erro ao criar tarefa');
            }
        });

        async function deleteTask(taskId) {
            if (confirm('Tem certeza que deseja excluir esta tarefa?')) {
                try {
                    const response = await api.delete('/api/tasks.php', { taskId });
                    if (response.success) {
                        window.location.reload();
                    }
                } catch (error) {
                    console.error('Erro ao excluir tarefa:', error);
                    alert('Erro ao excluir tarefa');
                }
            }
        }
    </script>
</body>
</html>

<?php
// Funções auxiliares para estilos
function getPriorityColor($priority) {
    switch ($priority) {
        case 'high': return 'border-red-500';
        case 'medium': return 'border-yellow-500';
        case 'low': return 'border-green-500';
        default: return 'border-gray-300';
    }
}

function getPriorityTextColor($priority) {
    switch ($priority) {
        case 'high': return 'text-red-600';
        case 'medium': return 'text-yellow-600';
        case 'low': return 'text-green-600';
        default: return 'text-gray-600';
    }
}
?>