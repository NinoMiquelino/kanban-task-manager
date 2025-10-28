<?php
// includes/header.php
if (!isset($pdo)) {
    include 'config.php';
}
?>
<header class="bg-white shadow-lg">
    <div class="container mx-auto px-4 py-4">
        <div class="flex justify-between items-center">
            <!-- Logo -->
            <div class="flex items-center space-x-3">
                <a href="index.php" class="flex items-center space-x-3 text-blue-600 hover:text-blue-700">
                    <i class="fas fa-tasks text-2xl"></i>
                    <span class="text-xl font-bold">Kanban Manager</span>
                </a>
            </div>

            <!-- User Menu -->
            <div class="flex items-center space-x-4">
                <?php if (isLoggedIn()): ?>
                    <div class="flex items-center space-x-3">
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars(getCurrentUserName()); ?></p>
                            <p class="text-xs text-gray-500"><?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
                        </div>
                        <div class="relative group">
                            <button class="flex items-center space-x-2 bg-gray-100 hover:bg-gray-200 rounded-full p-2 transition-colors">
                                <i class="fas fa-user-circle text-gray-600"></i>
                                <i class="fas fa-chevron-down text-xs text-gray-500"></i>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 hidden group-hover:block z-50">
                                <a href="index.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-th-large mr-2"></i>Meus Boards
                                </a>
                                <a href="profile.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i>Meu Perfil
                                </a>
                                <div class="border-t my-1"></div>
                                <a href="?logout=1" class="block px-4 py-2 text-red-600 hover:bg-red-50">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Sair
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="flex items-center space-x-3">
                        <a href="login.php" class="text-gray-600 hover:text-blue-600 font-medium">Entrar</a>
                        <a href="register.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium">
                            Cadastrar
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<style>
.group:hover .group-hover\:block {
    display: block !important;
}
</style>