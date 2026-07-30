<?php

declare(strict_types=1);

namespace Controllers;

use Repositories\EstadisticasRepository;
use Support\JsonResponse;

class EstadisticasController
{
    public static function pacientes(): void
    {
        $repo = new EstadisticasRepository();

        $data = [
            'resumen' => $repo->resumenGeneral(),
            'por_ciudad' => $repo->porCiudad(),
            'ciudad_barrio' => $repo->porCiudadBarrio(),
            'por_genero' => $repo->porGenero(),
            'por_edad' => $repo->porEdad(),
            'por_ocupacion' => $repo->porOcupacion(),
            'por_estado_civil' => $repo->porEstadoCivil(),
            'por_anio' => $repo->porAnio(),
            'por_mes' => $repo->porMesAnioActual(),
            'por_plan' => $repo->porPlan(),
        ];

        JsonResponse::success($data);
    }
}
