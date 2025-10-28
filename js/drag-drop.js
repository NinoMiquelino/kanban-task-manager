// js/drag-drop.js
class DragDropManager {
    constructor() {
        this.draggedTask = null;
        this.draggedTaskElement = null;
        this.setupEventListeners();
    }

    setupEventListeners() {
        document.addEventListener('dragstart', this.handleDragStart.bind(this));
        document.addEventListener('dragover', this.handleDragOver.bind(this));
        document.addEventListener('dragleave', this.handleDragLeave.bind(this));
        document.addEventListener('drop', this.handleDrop.bind(this));
        document.addEventListener('dragend', this.handleDragEnd.bind(this));
    }

    handleDragStart(e) {
        if (e.target.classList.contains('task')) {
            this.draggedTaskElement = e.target;
            this.draggedTask = {
                id: e.target.dataset.taskId,
                element: e.target
            };
            
            e.target.classList.add('opacity-50', 'dragging');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', e.target.dataset.taskId);
        }
    }

    handleDragOver(e) {
        e.preventDefault();
        
        const column = e.target.closest('.tasks-container');
        if (column) {
            column.classList.add('bg-blue-50', 'border-2', 'border-blue-300', 'border-dashed');
        }
        
        e.dataTransfer.dropEffect = 'move';
    }

    handleDragLeave(e) {
        const column = e.target.closest('.tasks-container');
        if (column && !column.contains(e.relatedTarget)) {
            column.classList.remove('bg-blue-50', 'border-2', 'border-blue-300', 'border-dashed');
        }
    }

    async handleDrop(e) {
        e.preventDefault();
        
        const column = e.target.closest('.tasks-container');
        if (column && this.draggedTask) {
            const newColumnId = column.closest('[data-column-id]').dataset.columnId;
            
            // Atualiza visualmente
            column.appendChild(this.draggedTaskElement);
            column.classList.remove('bg-blue-50', 'border-2', 'border-blue-300', 'border-dashed');
            
            // Atualiza no backend
            await this.updateTaskColumn(this.draggedTask.id, newColumnId);
        }
    }

    handleDragEnd(e) {
        if (this.draggedTaskElement) {
            this.draggedTaskElement.classList.remove('opacity-50', 'dragging');
        }
        
        // Limpa estilos de todas as colunas
        document.querySelectorAll('.tasks-container').forEach(container => {
            container.classList.remove('bg-blue-50', 'border-2', 'border-blue-300', 'border-dashed');
        });
        
        this.draggedTask = null;
        this.draggedTaskElement = null;
    }

    async updateTaskColumn(taskId, newColumnId) {
        try {
            const response = await api.put('/api/tasks.php', {
                taskId: taskId,
                columnId: newColumnId
            });
            
            if (!response.success) {
                throw new Error('Falha ao atualizar tarefa');
            }
            
            console.log('Tarefa movida com sucesso');
        } catch (error) {
            console.error('Erro ao mover tarefa:', error);
            alert('Erro ao mover tarefa. Recarregando página...');
            window.location.reload();
        }
    }
}

// Funções globais para eventos HTML
function allowDrop(e) {
    e.preventDefault();
}

function dragStart(e) {
    e.dataTransfer.setData('text/plain', e.target.dataset.taskId);
}

function drop(e) {
    e.preventDefault();
    const taskId = e.dataTransfer.getData('text/plain');
    const taskElement = document.querySelector(`[data-task-id="${taskId}"]`);
    const column = e.target.closest('.tasks-container');
    
    if (taskElement && column) {
        const newColumnId = column.closest('[data-column-id]').dataset.columnId;
        column.appendChild(taskElement);
        
        // Atualiza no backend
        fetch('/api/tasks.php', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                taskId: taskId,
                columnId: newColumnId
            })
        }).then(response => response.json())
          .then(data => {
              if (!data.success) {
                  throw new Error('Falha ao atualizar');
              }
          })
          .catch(error => {
              console.error('Erro:', error);
              alert('Erro ao mover tarefa');
              window.location.reload();
          });
    }
}

// Inicializa quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', () => {
    new DragDropManager();
});