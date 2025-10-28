<?php
// includes/functions.php

function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function jsonResponse($success, $data = [], $message = '') {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'data' => $data,
        'message' => $message
    ]);
    exit;
}

function formatDate($date, $format = 'd/m/Y') {
    if (empty($date)) return '';
    return date($format, strtotime($date));
}

function getPriorityBadge($priority) {
    $badges = [
        'low' => '<span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Baixa</span>',
        'medium' => '<span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">Média</span>',
        'high' => '<span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded">Alta</span>'
    ];
    
    return $badges[$priority] ?? $badges['medium'];
}

function getTaskCountByColumn($column_id, $pdo) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE column_id = ?");
    $stmt->execute([$column_id]);
    return $stmt->fetchColumn();
}

function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}
?>