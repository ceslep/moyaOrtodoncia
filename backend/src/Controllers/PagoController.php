<?php

declare(strict_types=1);

namespace Controllers;

use Repositories\PagoRepository;
use Support\JsonResponse;

class PagoController
{
    public static function index(): void
    {
        JsonResponse::error('Use /api/pacientes/{ind}/pagos', 400);
    }
}
