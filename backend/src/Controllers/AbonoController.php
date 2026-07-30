<?php

declare(strict_types=1);

namespace Controllers;

use Repositories\AbonoRepository;
use Support\JsonResponse;
use Support\Pagination;
use Support\Validator;

class AbonoController
{
    public static function index(): void
    {
        $desde = Validator::date($_GET['desde'] ?? null);
        $hasta = Validator::date($_GET['hasta'] ?? null);
        $formaPago = Validator::string($_GET['forma_de_pago'] ?? '');
        $p = Pagination::params();

        $repo = new AbonoRepository();
        $result = $repo->findGlobal(
            $desde,
            $hasta,
            $formaPago !== '' ? $formaPago : null,
            $p['offset'],
            $p['per_page']
        );
        JsonResponse::paginated($result['data'], $p['page'], $p['per_page'], $result['total']);
    }
}
