<?php

declare(strict_types=1);

namespace Controllers;

use Repositories\CitaRepository;
use Support\JsonResponse;
use Support\Pagination;
use Support\Validator;

class CitaController
{
    public static function index(): void
    {
        $desde = Validator::date($_GET['desde'] ?? null);
        $hasta = Validator::date($_GET['hasta'] ?? null);
        $especialista = Validator::string($_GET['especialista'] ?? '');
        $consultorio = Validator::string($_GET['consultorio'] ?? '');
        $estado = Validator::string($_GET['estado'] ?? '');
        $p = Pagination::params();

        $repo = new CitaRepository();
        $result = $repo->findGlobal(
            $desde,
            $hasta,
            $especialista !== '' ? $especialista : null,
            $consultorio !== '' ? $consultorio : null,
            $estado !== '' ? $estado : null,
            $p['offset'],
            $p['per_page']
        );
        JsonResponse::paginated($result['data'], $p['page'], $p['per_page'], $result['total']);
    }
}
