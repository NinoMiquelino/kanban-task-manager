// js/modal.js
class ModalManager {
    constructor() {
        this.activeModal = null;
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Close modal on background click
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal-backdrop')) {
                this.closeActiveModal();
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.activeModal) {
                this.closeActiveModal();
            }
        });

        // Prevent modal content click from closing modal
        document.addEventListener('click', (e) => {
            if (e.target.closest('.modal-content')) {
                e.stopPropagation();
            }
        });
    }

    openModal(modalId) {
        this.closeActiveModal();
        
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            this.activeModal = modal;
            
            // Focus first input if exists
            const firstInput = modal.querySelector('input, textarea, select');
            if (firstInput) {
                setTimeout(() => firstInput.focus(), 100);
            }
            
            // Add animation
            modal.classList.add('modal-enter');
        }
    }

    closeActiveModal() {
        if (this.activeModal) {
            this.activeModal.classList.add('hidden');
            this.activeModal.classList.remove('flex', 'modal-enter');
            this.activeModal = null;
        }
    }

    createModal(options) {
        const {
            title,
            content,
            onConfirm,
            onCancel,
            confirmText = 'Confirmar',
            cancelText = 'Cancelar',
            type = 'default'
        } = options;

        const modalId = 'dynamic-modal-' + Date.now();
        
        const modalHTML = `
            <div id="${modalId}" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 modal-backdrop">
                <div class="modal-content bg-white rounded-lg p-6 w-96 max-w-90vw max-h-90vh overflow-y-auto">
                    <h3 class="text-xl font-semibold mb-4">${title}</h3>
                    <div class="modal-body mb-6">
                        ${content}
                    </div>
                    <div class="flex justify-end space-x-3">
                        ${onCancel ? `
                            <button type="button" class="modal-cancel px-4 py-2 text-gray-600 hover:text-gray-800">
                                ${cancelText}
                            </button>
                        ` : ''}
                        <button type="button" class="modal-confirm px-4 py-2 rounded ${
                            type === 'danger' 
                                ? 'bg-red-500 hover:bg-red-600 text-white' 
                                : 'bg-blue-500 hover:bg-blue-600 text-white'
                        }">
                            ${confirmText}
                        </button>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        const modal = document.getElementById(modalId);
        
        // Setup event listeners
        const confirmBtn = modal.querySelector('.modal-confirm');
        const cancelBtn = modal.querySelector('.modal-cancel');
        
        confirmBtn.addEventListener('click', () => {
            if (onConfirm) onConfirm();
            this.closeModal(modalId);
        });
        
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                if (onCancel) onCancel();
                this.closeModal(modalId);
            });
        }

        this.openModal(modalId);
        return modalId;
    }

    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            setTimeout(() => modal.remove(), 300);
        }
        
        if (this.activeModal && this.activeModal.id === modalId) {
            this.activeModal = null;
        }
    }

    // Utility method for confirmation dialogs
    confirm(options) {
        return new Promise((resolve) => {
            this.createModal({
                ...options,
                onConfirm: () => resolve(true),
                onCancel: () => resolve(false)
            });
        });
    }

    // Utility method for alert dialogs
    alert(message, title = 'Aviso') {
        return new Promise((resolve) => {
            this.createModal({
                title: title,
                content: `<p class="text-gray-700">${message}</p>`,
                confirmText: 'OK',
                onConfirm: () => resolve(true),
                onCancel: null
            });
        });
    }
}

// Global modal functions for HTML onclick attributes
function openCreateBoardModal() {
    modals.openModal('createBoardModal');
}

function closeCreateBoardModal() {
    modals.closeActiveModal();
}

function openCreateTaskModal(columnId = null) {
    if (columnId) {
        document.getElementById('taskColumnId').value = columnId;
    }
    modals.openModal('createTaskModal');
}

function closeCreateTaskModal() {
    modals.closeActiveModal();
    document.getElementById('createTaskForm').reset();
}

// Initialize modal manager
const modals = new ModalManager();