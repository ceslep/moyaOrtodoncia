<?php

declare(strict_types=1);

namespace Controllers;

use Repositories\DashboardRepository;
use Support\JsonResponse;

class DashboardController
{
    public static function resumen(): void
    {
        $repo = new DashboardRepository();
        $data = $repo->resumen();
        JsonResponse::success($data);
    }

    public static function datosEmpresa(): void
    {
        $repo = new DashboardRepository();
        $data = $repo->datosEmpresa();
        JsonResponse::success($data ?? []);
    }
}
