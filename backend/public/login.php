<?php

declare(strict_types=1);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

require_once dirname(__DIR__) . '/src/Config/Database.php';
require_once dirname(__DIR__) . '/src/Support/JsonResponse.php';
require_once dirname(__DIR__) . '/src/Repositories/AuthRepository.php';

use Repositories\AuthRepository;

try {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || empty($input['usuario']) || empty($input['password'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Usuario y password requeridos']);
        exit;
    }

    $usuario = trim($input['usuario']);
    $password = $input['password'];

    $authRepo = new AuthRepository();
    $user = $authRepo->findByUsuario($usuario);

    if (!$user || !password_verify($password, $user['password'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Credenciales incorrectas']);
        exit;
    }

    // Limpiar tokens expirados
    $authRepo->deleteExpiredTokens();

    // Generar token
    $token = $authRepo->createToken((int)$user['id']);

    echo json_encode([
        'success' => true,
        'token'   => $token,
        'user'    => [
            'id'       => (int)$user['id'],
            'usuario'  => $user['usuario'],
        ],
    ]);
} catch (\Throwable $e) {
    error_log('Login Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error interno del servidor']);
}
