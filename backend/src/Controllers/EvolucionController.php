<?php

declare(strict_types=1);

namespace Controllers;

use Repositories\EvolucionRepository;
use Support\JsonResponse;

class EvolucionController
{
    public static function index(): void
    {
        JsonResponse::error('Use /api/pacientes/{ind}/evoluciones', 400);
    }
}
