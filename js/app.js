// js/app.js
class KanbanApp {
    constructor() {
        this.currentBoard = null;
        this.boards = [];
        this.columns = [];
        this.tasks = [];
        
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadInitialData();
        this.setupServiceWorker();
    }

    setupEventListeners() {
        // Logout confirmation
        const logoutLinks = document.querySelectorAll('a[href*="logout"]');
        logoutLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                if (!confirm('Tem certeza que deseja sair?')) {
                    e.preventDefault();
                }
            });
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', this.handleKeyboardShortcuts.bind(this));
        
        // Before unload - warn about unsaved changes
        window.addEventListener('beforeunload', this.handleBeforeUnload.bind(this));
    }

    handleKeyboardShortcuts(e) {
        // Ctrl/Cmd + N - New task
        if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
            e.preventDefault();
            this.openCreateTaskModal();
        }
        
        // Escape - Close modals
        if (e.key === 'Escape') {
            this.closeAllModals();
        }
    }

    handleBeforeUnload(e) {
        // You can implement dirty form checking here
        // For now, we'll just allow navigation
    }

    async loadInitialData() {
        try {
            // Load user's boards if on index page
            if (window.location.pathname.endsWith('index.php')) {
                await this.loadBoards();
            }
            
            // Load board data if on board page
            if (window.location.pathname.endsWith('board.php')) {
                await this.loadBoardData();
            }
        } catch (error) {
            console.error('Erro ao carregar dados iniciais:', error);
            this.showNotification('Erro ao carregar dados', 'error');
        }
    }

    async loadBoards() {
        try {
            const response = await api.get('/api/boards.php');
            if (response.success) {
                this.boards = response.boards;
                this.renderBoards();
            }
        } catch (error) {
            console.error('Erro ao carregar boards:', error);
        }
    }

    async loadBoardData() {
        const urlParams = new URLSearchParams(window.location.search);
        const boardId = urlParams.get('id');
        
        if (!boardId) {
            this.showNotification('Board não encontrado', 'error');
            return;
        }

        this.currentBoard = boardId;
        
        try {
            // Load columns
            const columnsResponse = await api.get(`/api/columns.php?board_id=${boardId}`);
            if (columnsResponse.success) {
                this.columns = columnsResponse.columns;
            }
            
            // You can load tasks here if needed
        } catch (error) {
            console.error('Erro ao carregar dados do board:', error);
            this.showNotification('Erro ao carregar board', 'error');
        }
    }

    renderBoards() {
        const boardsContainer = document.querySelector('#boards-container');
        if (!boardsContainer) return;

        if (this.boards.length === 0) {
            boardsContainer.innerHTML = this.getEmptyStateHTML();
            return;
        }

        boardsContainer.innerHTML = this.boards.map(board => this.getBoardCardHTML(board)).join('');
    }

    getEmptyStateHTML() {
        return `
            <div class="col-span-full text-center py-12">
                <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">Nenhum board encontrado</h3>
                <p class="text-gray-500 mb-4">Crie seu primeiro board para começar a organizar suas tarefas</p>
                <button onclick="openCreateBoardModal()" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg">
                    Criar Primeiro Board
                </button>
            </div>
        `;
    }

    getBoardCardHTML(board) {
        return `
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-xl font-semibold text-gray-800">${this.escapeHTML(board.name)}</h3>
                    <div class="flex space-x-2">
                        <button onclick="app.editBoard(${board.id})" 
                                class="text-gray-400 hover:text-blue-500 p-1">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="app.deleteBoard(${board.id})" 
                                class="text-gray-400 hover:text-red-500 p-1">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                
                ${board.description ? `
                    <p class="text-gray-600 text-sm mb-4">${this.escapeHTML(board.description)}</p>
                ` : ''}
                
                <div class="flex justify-between items-center text-sm text-gray-500">
                    <span>
                        <i class="fas fa-tasks mr-1"></i>
                        ${board.total_tasks || 0} tarefas
                    </span>
                    <span>
                        <i class="fas fa-calendar mr-1"></i>
                        ${this.formatDate(board.created_at)}
                    </span>
                </div>
                
                <div class="mt-4">
                    <a href="board.php?id=${board.id}" 
                       class="block w-full bg-blue-500 hover:bg-blue-600 text-white text-center py-2 rounded-lg transition-colors">
                        <i class="fas fa-external-link-alt mr-2"></i>Abrir Board
                    </a>
                </div>
            </div>
        `;
    }

    async createBoard(boardData) {
        try {
            const response = await api.post('/api/boards.php', boardData);
            if (response.success) {
                this.showNotification('Board criado com sucesso!', 'success');
                await this.loadBoards();
                return response.boardId;
            }
        } catch (error) {
            console.error('Erro ao criar board:', error);
            this.showNotification('Erro ao criar board', 'error');
            throw error;
        }
    }

    async deleteBoard(boardId) {
        if (!confirm('Tem certeza que deseja excluir este board? Todas as tarefas serão perdidas.')) {
            return;
        }

        try {
            const response = await api.delete('/api/boards.php', { boardId });
            if (response.success) {
                this.showNotification('Board excluído com sucesso!', 'success');
                await this.loadBoards();
            }
        } catch (error) {
            console.error('Erro ao excluir board:', error);
            this.showNotification('Erro ao excluir board', 'error');
        }
    }

    async editBoard(boardId) {
        // Implement board editing functionality
        const board = this.boards.find(b => b.id == boardId);
        if (board) {
            // You can implement a modal for editing
            const newName = prompt('Novo nome do board:', board.name);
            if (newName && newName !== board.name) {
                try {
                    const response = await api.put('/api/boards.php', {
                        boardId: boardId,
                        name: newName,
                        description: board.description
                    });
                    
                    if (response.success) {
                        this.showNotification('Board atualizado com sucesso!', 'success');
                        await this.loadBoards();
                    }
                } catch (error) {
                    console.error('Erro ao atualizar board:', error);
                    this.showNotification('Erro ao atualizar board', 'error');
                }
            }
        }
    }

    showNotification(message, type = 'info') {
        // Remove existing notifications
        const existingNotifications = document.querySelectorAll('.app-notification');
        existingNotifications.forEach(notification => notification.remove());

        const notification = document.createElement('div');
        notification.className = `app-notification fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-transform duration-300 ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            type === 'warning' ? 'bg-yellow-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        
        notification.innerHTML = `
            <div class="flex items-center space-x-3">
                <i class="fas fa-${this.getNotificationIcon(type)}"></i>
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        document.body.appendChild(notification);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.remove();
                    }
                }, 300);
            }
        }, 5000);
    }

    getNotificationIcon(type) {
        const icons = {
            success: 'check-circle',
            error: 'exclamation-triangle',
            warning: 'exclamation-circle',
            info: 'info-circle'
        };
        return icons[type] || 'info-circle';
    }

    escapeHTML(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('pt-BR');
    }

    closeAllModals() {
        const modals = document.querySelectorAll('.modal-open');
        modals.forEach(modal => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        });
    }

    setupServiceWorker() {
        // You can implement PWA features here
        if ('serviceWorker' in navigator) {
            // navigator.serviceWorker.register('/sw.js');
        }
    }
}

// Initialize the app
const app = new KanbanApp();