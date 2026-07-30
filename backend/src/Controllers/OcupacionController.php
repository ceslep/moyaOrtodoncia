<?php

declare(strict_types=1);

namespace Controllers;

use Repositories\OcupacionRepository;
use Support\JsonResponse;
use Support\Validator;

class OcupacionController
{
    public static function byCodigo(string $codigo): void
    {
        $repo = new OcupacionRepository();
        $data = $repo->findByCodigo($codigo);
        JsonResponse::success($data ? $data : []);
    }

    public static function search(): void
    {
        $search = Validator::string($_GET['search'] ?? '');
        $repo = new OcupacionRepository();
        $data = $repo->search($search);
        JsonResponse::success($data);
    }
}
