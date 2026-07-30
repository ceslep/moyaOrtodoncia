<?php

declare(strict_types=1);

namespace Controllers;

use Repositories\HojaVidaRepository;
use Support\JsonResponse;
use Support\Pagination;
use Support\Validator;

class HojaVidaController
{
    public static function index(): void
    {
        $search = Validator::string($_GET['search'] ?? '');
        $p = Pagination::params();
        $repo = new HojaVidaRepository();
        $result = $repo->search($search, $p['offset'], $p['per_page']);
        JsonResponse::paginated($result['data'], $p['page'], $p['per_page'], $result['total']);
    }

    public static function show(int $ind, int $identificacion): void
    {
        $repo = new HojaVidaRepository();
        $data = $repo->findByIdentificacion($ind, $identificacion);
        if (!$data) {
            JsonResponse::notFound('Personal no encontrado');
        }
        JsonResponse::success($data);
    }
}
