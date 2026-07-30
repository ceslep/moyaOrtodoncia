<?php

declare(strict_types=1);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

require_once dirname(__DIR__) . '/src/Config/Database.php';
require_once dirname(__DIR__) . '/src/Support/JsonResponse.php';
require_once dirname(__DIR__) . '/src/Support/Pagination.php';
require_once dirname(__DIR__) . '/src/Support/Validator.php';
require_once dirname(__DIR__) . '/src/Repositories/PacienteRepository.php';
require_once dirname(__DIR__) . '/src/Repositories/CitaRepository.php';
require_once dirname(__DIR__) . '/src/Repositories/EvolucionRepository.php';
require_once dirname(__DIR__) . '/src/Repositories/PagoRepository.php';
require_once dirname(__DIR__) . '/src/Repositories/AbonoRepository.php';
require_once dirname(__DIR__) . '/src/Repositories/CatalogoRepository.php';
require_once dirname(__DIR__) . '/src/Repositories/HojaVidaRepository.php';
require_once dirname(__DIR__) . '/src/Repositories/DashboardRepository.php';
require_once dirname(__DIR__) . '/src/Repositories/MunicipioRepository.php';
require_once dirname(__DIR__) . '/src/Repositories/OcupacionRepository.php';
require_once dirname(__DIR__) . '/src/Controllers/PacienteController.php';
require_once dirname(__DIR__) . '/src/Controllers/CitaController.php';
require_once dirname(__DIR__) . '/src/Controllers/EvolucionController.php';
require_once dirname(__DIR__) . '/src/Controllers/PagoController.php';
require_once dirname(__DIR__) . '/src/Controllers/AbonoController.php';
require_once dirname(__DIR__) . '/src/Controllers/CatalogoController.php';
require_once dirname(__DIR__) . '/src/Controllers/HojaVidaController.php';
require_once dirname(__DIR__) . '/src/Controllers/DashboardController.php';
require_once dirname(__DIR__) . '/src/Controllers/MunicipioController.php';
require_once dirname(__DIR__) . '/src/Controllers/OcupacionController.php';

use Support\JsonResponse;

try {
    $route = $_GET['route'] ?? '';
    $route = '/' . trim($route, '/');
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($method !== 'GET') {
        JsonResponse::error('Method not allowed', 405);
    }

    $parts = explode('/', trim($route, '/'));

    // Route: /api/pacientes/{ind}/foto
    if (preg_match('#^/api/pacientes/(\d+)/foto$#', $route, $m)) {
        \Controllers\PacienteController::foto((int)$m[1]);
    }
    // Route: /api/pacientes/{ind}/historia-clinica
    elseif (preg_match('#^/api/pacientes/(\d+)/historia-clinica$#', $route, $m)) {
        \Controllers\PacienteController::historiaClinica((int)$m[1]);
    }
    // Route: /api/pacientes/{ind}/citas-canceladas
    elseif (preg_match('#^/api/pacientes/(\d+)/citas-canceladas$#', $route, $m)) {
        \Controllers\PacienteController::citasCanceladas((int)$m[1]);
    }
    // Route: /api/pacientes/{ind}/citas
    elseif (preg_match('#^/api/pacientes/(\d+)/citas$#', $route, $m)) {
        \Controllers\PacienteController::citas((int)$m[1]);
    }
    // Route: /api/pacientes/{ind}/evoluciones
    elseif (preg_match('#^/api/pacientes/(\d+)/evoluciones$#', $route, $m)) {
        \Controllers\PacienteController::evoluciones((int)$m[1]);
    }
    // Route: /api/pacientes/{ind}/abonos
    elseif (preg_match('#^/api/pacientes/(\d+)/abonos$#', $route, $m)) {
        \Controllers\PacienteController::abonos((int)$m[1]);
    }
    // Route: /api/pacientes/{ind}/pagos
    elseif (preg_match('#^/api/pacientes/(\d+)/pagos$#', $route, $m)) {
        \Controllers\PacienteController::pagos((int)$m[1]);
    }
    // Route: /api/pacientes/{ind}/detalles-pagos
    elseif (preg_match('#^/api/pacientes/(\d+)/detalles-pagos$#', $route, $m)) {
        \Controllers\PacienteController::detallesPagos((int)$m[1]);
    }
    // Route: /api/pacientes/{ind}
    elseif (preg_match('#^/api/pacientes/(\d+)$#', $route, $m)) {
        \Controllers\PacienteController::show((int)$m[1]);
    }
    // Route: /api/pacientes
    elseif ($route === '/api/pacientes') {
        \Controllers\PacienteController::index();
    }
    // Route: /api/citas
    elseif ($route === '/api/citas') {
        \Controllers\CitaController::index();
    }
    // Route: /api/abonos
    elseif ($route === '/api/abonos') {
        \Controllers\AbonoController::index();
    }
    // Route: /api/procedimientos
    elseif ($route === '/api/procedimientos') {
        \Controllers\CatalogoController::procedimientos();
    }
    // Route: /api/especialidades
    elseif ($route === '/api/especialidades') {
        \Controllers\CatalogoController::especialidades();
    }
    // Route: /api/entidades
    elseif ($route === '/api/entidades') {
        \Controllers\CatalogoController::entidades();
    }
    // Route: /api/municipios/buscar
    elseif ($route === '/api/municipios/buscar') {
        \Controllers\MunicipioController::search();
    }
    // Route: /api/municipios/{codigo}
    elseif (preg_match('#^/api/municipios/([^/]+)$#', $route, $m)) {
        \Controllers\MunicipioController::byCodigo(urldecode($m[1]));
    }
    // Route: /api/ocupaciones/buscar
    elseif ($route === '/api/ocupaciones/buscar') {
        \Controllers\OcupacionController::search();
    }
    // Route: /api/ocupaciones/{codigo}
    elseif (preg_match('#^/api/ocupaciones/([^/]+)$#', $route, $m)) {
        \Controllers\OcupacionController::byCodigo(urldecode($m[1]));
    }
    // Route: /api/personal/{ind}/{identificacion}
    elseif (preg_match('#^/api/personal/(\d+)/(\d+)$#', $route, $m)) {
        \Controllers\HojaVidaController::show((int)$m[1], (int)$m[2]);
    }
    // Route: /api/personal
    elseif ($route === '/api/personal') {
        \Controllers\HojaVidaController::index();
    }
    // Route: /api/dashboard/resumen
    elseif ($route === '/api/dashboard/resumen') {
        \Controllers\DashboardController::resumen();
    }
    // Route: /api/datos-empresa
    elseif ($route === '/api/datos-empresa') {
        \Controllers\DashboardController::datosEmpresa();
    }
    else {
        JsonResponse::notFound('Ruta no encontrada: ' . $route);
    }
} catch (\Throwable $e) {
    error_log('API Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    JsonResponse::error('Error interno del servidor', 500);
}
