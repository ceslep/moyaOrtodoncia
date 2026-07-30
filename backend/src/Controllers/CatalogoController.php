<?php

declare(strict_types=1);

namespace Controllers;

use Repositories\CatalogoRepository;
use Support\JsonResponse;
use Support\Pagination;
use Support\Validator;

class CatalogoController
{
    public static function procedimientos(): void
    {
        $search = Validator::string($_GET['search'] ?? '');
        $p = Pagination::params();
        $repo = new CatalogoRepository();
        $result = $repo->procedimientos($search, $p['offset'], $p['per_page']);
        JsonResponse::paginated($result['data'], $p['page'], $p['per_page'], $result['total']);
    }

    public static function especialidades(): void
    {
        $search = Validator::string($_GET['search'] ?? '');
        $repo = new CatalogoRepository();
        $data = $repo->especialidades($search);
        JsonResponse::success($data);
    }

    public static function entidades(): void
    {
        $search = Validator::string($_GET['search'] ?? '');
        $repo = new CatalogoRepository();
        $data = $repo->entidades($search);
        JsonResponse::success($data);
    }
}
