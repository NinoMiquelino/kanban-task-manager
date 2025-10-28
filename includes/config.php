<?php
// includes/config.php
session_start();

define('DB_HOST', 'localhost');
define('DB_NAME', 'kanban_manager');
define('DB_USER', 'root');
define('DB_PASS', '');

// Conexão com o banco
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

// Função para verificar autenticação
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Função para gerar hash de senha
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Função para verificar senha
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}
?>