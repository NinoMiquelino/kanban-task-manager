<?php
// includes/auth.php

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function requireAuth() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getCurrentUserName() {
    return $_SESSION['user_name'] ?? 'Usuário';
}

function logout() {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Verificar se há tentativa de logout
if (isset($_GET['logout'])) {
    logout();
}
?>