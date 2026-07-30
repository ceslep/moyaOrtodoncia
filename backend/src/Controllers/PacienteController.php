<?php

declare(strict_types=1);

namespace Controllers;

use Repositories\PacienteRepository;
use Repositories\CitaRepository;
use Repositories\EvolucionRepository;
use Repositories\PagoRepository;
use Repositories\AbonoRepository;
use Support\JsonResponse;
use Support\Pagination;
use Support\Validator;

class PacienteController
{
    public static function index(): void
    {
        $search = Validator::string($_GET['search'] ?? '');
        $p = Pagination::params();
        $repo = new PacienteRepository();
        $result = $repo->search($search, $p['offset'], $p['per_page']);
        JsonResponse::paginated($result['data'], $p['page'], $p['per_page'], $result['total']);
    }

    public static function show(int $ind): void
    {
        $repo = new PacienteRepository();
        $paciente = $repo->findById($ind);
        if (!$paciente) {
            JsonResponse::notFound('Paciente no encontrado');
        }
        $cuotas = $repo->getCuotas($ind);
        $paciente['cuotas'] = $cuotas;
        JsonResponse::success($paciente);
    }

    public static function foto(int $ind): void
    {
        $repo = new PacienteRepository();
        $foto = $repo->findFoto($ind);
        if (!$foto) {
            JsonResponse::notFound('Foto no encontrada');
        }
        header('Content-Type: image/jpeg');
        header('Cache-Control: public, max-age=86400');
        echo $foto;
        exit;
    }

    public static function historiaClinica(int $ind): void
    {
        $repo = new PacienteRepository();
        $hist = $repo->getHistoria($ind);
        if (!$hist) {
            JsonResponse::notFound('Paciente no encontrado');
        }
        $db = \Config\Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM cppredata WHERE historia = :historia LIMIT 1");
        $stmt->bindValue(':historia', $hist['historia']);
        $stmt->execute();
        $data = $stmt->fetch();
        if (!$data) {
            JsonResponse::success([]);
            return;
        }
        unset($data['foto'], $data['Image1'], $data['Image2'], $data['voz']);
        JsonResponse::success($data);
    }

    public static function citas(int $ind): void
    {
        $repo = new PacienteRepository();
        $hist = $repo->getHistoria($ind);
        if (!$hist) {
            JsonResponse::notFound('Paciente no encontrado');
        }
        $estado = $_GET['estado'] ?? null;
        $desde = Validator::date($_GET['desde'] ?? null);
        $hasta = Validator::date($_GET['hasta'] ?? null);
        $citaRepo = new CitaRepository();
        $citas = $citaRepo->findByPaciente($hist['historia'], $estado, $desde, $hasta);
        JsonResponse::success($citas);
    }

    public static function citasCanceladas(int $ind): void
    {
        $repo = new PacienteRepository();
        $hist = $repo->getHistoria($ind);
        if (!$hist) {
            JsonResponse::notFound('Paciente no encontrado');
        }
        $citaRepo = new CitaRepository();
        $canceladas = $citaRepo->findCanceladasByPaciente($hist['historia']);
        JsonResponse::success($canceladas);
    }

    public static function evoluciones(int $ind): void
    {
        $repo = new PacienteRepository();
        $hist = $repo->getHistoria($ind);
        if (!$hist) {
            JsonResponse::notFound('Paciente no encontrado');
        }
        $evolRepo = new EvolucionRepository();
        $evoluciones = $evolRepo->findByPaciente($hist['historia']);
        JsonResponse::success($evoluciones);
    }

    public static function abonos(int $ind): void
    {
        $repo = new PacienteRepository();
        $hist = $repo->getHistoria($ind);
        if (!$hist) {
            JsonResponse::notFound('Paciente no encontrado');
        }
        $abonoRepo = new AbonoRepository();
        $abonos = $abonoRepo->findByPaciente($hist['historia']);
        JsonResponse::success($abonos);
    }

    public static function pagos(int $ind): void
    {
        $repo = new PacienteRepository();
        $hist = $repo->getHistoria($ind);
        if (!$hist) {
            JsonResponse::notFound('Paciente no encontrado');
        }
        $pagoRepo = new PagoRepository();
        $pagos = $pagoRepo->findByPaciente($hist['historia']);
        JsonResponse::success($pagos);
    }

    public static function detallesPagos(int $ind): void
    {
        $repo = new PacienteRepository();
        $hist = $repo->getHistoria($ind);
        if (!$hist) {
            JsonResponse::notFound('Paciente no encontrado');
        }
        $pagoRepo = new PagoRepository();
        $detalles = $pagoRepo->findDetallesByPaciente($hist['historia']);
        JsonResponse::success($detalles);
    }
}
