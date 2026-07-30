<?php

declare(strict_types=1);

namespace Repositories;

use Config\Database;

class EstadisticasRepository
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function porCiudad(): array
    {
        $sql = "SELECT COALESCE(m.nombre, 'Sin ciudad') AS ciudad,
                       COUNT(*) AS cantidad
                FROM paciente p
                LEFT JOIN municipios m ON m.codigo = p.ciudad_residencia
                WHERE p.estado = 'ACTIVO'
                GROUP BY m.nombre
                ORDER BY cantidad DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function porCiudadBarrio(): array
    {
        $sql = "SELECT COALESCE(m.nombre, 'Sin ciudad') AS ciudad,
                       COALESCE(NULLIF(TRIM(p.barrio), ''), 'Sin barrio') AS barrio,
                       COUNT(*) AS cantidad,
                       GROUP_CONCAT(DISTINCT p.direccion_residencia SEPARATOR ' | ') AS direcciones_ejemplo
                FROM paciente p
                LEFT JOIN municipios m ON m.codigo = p.ciudad_residencia
                WHERE p.estado = 'ACTIVO'
                GROUP BY m.nombre, p.barrio
                ORDER BY m.nombre, cantidad DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function porGenero(): array
    {
        $sql = "SELECT COALESCE(NULLIF(p.sexo, ''), 'No especificado') AS genero,
                       COUNT(*) AS cantidad
                FROM paciente p
                WHERE p.estado = 'ACTIVO'
                GROUP BY p.sexo
                ORDER BY cantidad DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function porEdad(): array
    {
        $sql = "SELECT
                    CASE
                        WHEN p.edad BETWEEN 0 AND 10 THEN '0-10'
                        WHEN p.edad BETWEEN 11 AND 20 THEN '11-20'
                        WHEN p.edad BETWEEN 21 AND 30 THEN '21-30'
                        WHEN p.edad BETWEEN 31 AND 40 THEN '31-40'
                        WHEN p.edad BETWEEN 41 AND 50 THEN '41-50'
                        WHEN p.edad BETWEEN 51 AND 60 THEN '51-60'
                        WHEN p.edad BETWEEN 61 AND 70 THEN '61-70'
                        WHEN p.edad > 70 THEN '70+'
                        ELSE 'No especificado'
                    END AS rango_edad,
                    COUNT(*) AS cantidad
                FROM paciente p
                WHERE p.estado = 'ACTIVO'
                GROUP BY rango_edad
                ORDER BY
                    CASE rango_edad
                        WHEN '0-10' THEN 1
                        WHEN '11-20' THEN 2
                        WHEN '21-30' THEN 3
                        WHEN '31-40' THEN 4
                        WHEN '41-50' THEN 5
                        WHEN '51-60' THEN 6
                        WHEN '61-70' THEN 7
                        WHEN '70+' THEN 8
                        ELSE 9
                    END";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function porOcupacion(): array
    {
        $sql = "SELECT COALESCE(o.nombre, 'Sin ocupación') AS ocupacion,
                       COUNT(*) AS cantidad
                FROM paciente p
                LEFT JOIN ocupaciones o ON CAST(o.codigo AS CHAR) = CAST(p.ocupacion AS CHAR)
                WHERE p.estado = 'ACTIVO'
                GROUP BY o.nombre
                ORDER BY cantidad DESC
                LIMIT 15";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function porEstadoCivil(): array
    {
        $sql = "SELECT COALESCE(NULLIF(p.estado_civil, ''), 'No especificado') AS estado_civil,
                       COUNT(*) AS cantidad
                FROM paciente p
                WHERE p.estado = 'ACTIVO'
                GROUP BY p.estado_civil
                ORDER BY cantidad DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function porAnio(): array
    {
        $sql = "SELECT YEAR(p.fecha_inicio) AS anio,
                       COUNT(*) AS cantidad
                FROM paciente p
                WHERE p.estado = 'ACTIVO' AND p.fecha_inicio IS NOT NULL
                GROUP BY YEAR(p.fecha_inicio)
                ORDER BY anio ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function porMesAnioActual(): array
    {
        $sql = "SELECT MONTH(p.fecha_inicio) AS mes,
                       COUNT(*) AS cantidad
                FROM paciente p
                WHERE p.estado = 'ACTIVO'
                  AND YEAR(p.fecha_inicio) = YEAR(CURDATE())
                  AND p.fecha_inicio IS NOT NULL
                GROUP BY MONTH(p.fecha_inicio)
                ORDER BY mes ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function porPlan(): array
    {
        $sql = "SELECT COALESCE(NULLIF(p.plan, ''), 'No especificado') AS plan_pago,
                       COUNT(*) AS cantidad
                FROM paciente p
                WHERE p.estado = 'ACTIVO'
                GROUP BY p.plan
                ORDER BY cantidad DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function resumenGeneral(): array
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM paciente WHERE estado = 'ACTIVO'");
        $total = (int)$stmt->fetchColumn();

        $stmt = $this->db->query("SELECT COUNT(DISTINCT p.ciudad_residencia) FROM paciente p WHERE p.estado = 'ACTIVO'");
        $totalCiudades = (int)$stmt->fetchColumn();

        $stmt = $this->db->query("SELECT ROUND(AVG(p.edad), 1) FROM paciente p WHERE p.estado = 'ACTIVO' AND p.edad > 0");
        $edadPromedio = (float)$stmt->fetchColumn();

        $stmt = $this->db->query("SELECT MIN(p.fecha_inicio) FROM paciente p WHERE p.estado = 'ACTIVO' AND p.fecha_inicio IS NOT NULL");
        $primerPaciente = $stmt->fetchColumn();

        return [
            'total_pacientes' => $total,
            'total_ciudades' => $totalCiudades,
            'edad_promedio' => $edadPromedio,
            'primer_paciente' => $primerPaciente,
        ];
    }
}
