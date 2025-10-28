<?php
// index.php
include 'includes/config.php';
include 'includes/auth.php';

// Redirecionar se não estiver logado
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Buscar boards do usuário
$stmt = $pdo->prepare("SELECT * FROM boards WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$boards = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Boards - Kanban Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Meus Boards</h1>
            <button onclick="openCreateBoardModal()" 
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
                <i class="fas fa-plus"></i>
                <span>Novo Board</span>
            </button>
        </div>

        <!-- Boards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php if (empty($boards)): ?>
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">Nenhum board encontrado</h3>
                    <p class="text-gray-500 mb-4">Crie seu primeiro board para começar a organizar suas tarefas</p>
                    <button onclick="openCreateBoardModal()" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg">
                        Criar Primeiro Board
                    </button>
                </div>
            <?php else: ?>
                <?php foreach ($boards as $board): ?>
                    <a href="board.php?id=<?php echo $board['id']; ?>" 
                       class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6 block group">
                        <div class="flex items-start justify-between mb-4">
                            <h3 class="text-xl font-semibold text-gray-800 group-hover:text-blue-600">
                                <?php echo htmlspecialchars($board['name']); ?>
                            </h3>
                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-blue-500"></i>
                        </div>
                        
                        <?php if ($board['description']): ?>
                            <p class="text-gray-600 text-sm mb-4">
                                <?php echo htmlspecialchars($board['description']); ?>
                            </p>
                        <?php endif; ?>
                        
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-calendar mr-2"></i>
                            <span>Criado em <?php echo date('d/m/Y', strtotime($board['created_at'])); ?></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <!-- Create Board Modal -->
    <div id="createBoardModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-96">
            <h3 class="text-xl font-semibold mb-4">Criar Novo Board</h3>
            <form id="createBoardForm">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nome do Board</label>
                    <input type="text" name="name" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Descrição (opcional)</label>
                    <textarea name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeCreateBoardModal()" 
                            class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancelar</button>
                    <button type="submit" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Criar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="js/app.js"></script>
    <script src="js/modal.js"></script>
    <script src="js/api.js"></script>
    <script src="js/drag-drop.js"></script>
    <script>
        let currentColumnId = null;

        // Funções de modal
        function openCreateTaskModal(columnId = null) {
            currentColumnId = columnId;
            document.getElementById('taskColumnId').value = columnId;
            modals.openModal('createTaskModal');
        }

        function closeCreateTaskModal() {
            modals.closeActiveModal();
            document.getElementById('createTaskForm').reset();
        }

        // Form submission para criar tarefa
        document.getElementById('createTaskForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData);
            
            try {
                const response = await api.post('/api/tasks.php', data);
                if (response.success) {
                    window.location.reload(); // Recarrega para mostrar a nova tarefa
                }
            } catch (error) {
                console.error('Erro ao criar tarefa:', error);
                app.showNotification('Erro ao criar tarefa', 'error');
            }
        });

        // Função para excluir tarefa
        async function deleteTask(taskId) {
            const confirmed = await modals.confirm({
                title: 'Confirmar Exclusão',
                content: '<p class="text-gray-700">Tem certeza que deseja excluir esta tarefa?</p>',
                type: 'danger',
                confirmText: 'Excluir',
                cancelText: 'Cancelar'
            });
            
            if (confirmed) {
                try {
                    const response = await api.delete('/api/tasks.php', { taskId });
                    if (response.success) {
                        window.location.reload();
                    }
                } catch (error) {
                    console.error('Erro ao excluir tarefa:', error);
                    app.showNotification('Erro ao excluir tarefa', 'error');
                }
            }
        }

        // Função para editar tarefa (placeholder - pode ser implementada depois)
        function editTask(taskId) {
            app.showNotification('Funcionalidade de edição em desenvolvimento', 'info');
        }
    </script>
</body>
</html>