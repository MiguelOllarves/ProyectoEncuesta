<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No se enviaron credenciales.']);
    exit;
}

$usuario = trim((string)($data['usuario'] ?? ''));
$password = trim((string)($data['password'] ?? ''));

$adminUser = 'admin';
$adminPass = 'admin123';

if ($usuario === $adminUser && $password === $adminPass) {
    $_SESSION['auth'] = true;
    $_SESSION['user'] = $usuario;
    $_SESSION['login_time'] = time();

    $token = base64_encode('sesion_valida_' . time());
    echo json_encode([
        'success' => true,
        'token' => $token,
        'mensaje' => 'Acceso autorizado'
    ]);
    exit;
}

http_response_code(401);
echo json_encode(['success' => false, 'error' => 'Usuario o contraseña incorrectos.']);
