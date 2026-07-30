<?php

declare(strict_types=1);

namespace Controllers;

use Repositories\MunicipioRepository;
use Support\JsonResponse;
use Support\Validator;

class MunicipioController
{
    public static function byCodigo(string $codigo): void
    {
        $repo = new MunicipioRepository();
        $data = $repo->findByCodigo($codigo);
        JsonResponse::success($data ? $data : []);
    }

    public static function search(): void
    {
        $search = Validator::string($_GET['search'] ?? '');
        $repo = new MunicipioRepository();
        $data = $repo->search($search);
        JsonResponse::success($data);
    }
}
