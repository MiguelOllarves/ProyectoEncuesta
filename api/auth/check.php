<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Sesión inválida.']);
    exit;
}

echo json_encode([
    'success' => true,
    'user' => $_SESSION['user'] ?? 'admin'
]);
