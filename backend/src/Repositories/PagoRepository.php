<?php

declare(strict_types=1);

namespace Repositories;

use Config\Database;

class PagoRepository
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findByPaciente(int $historia): array
    {
        $sql = "SELECT pg.ind, pg.paciente, pg.tipo, pg.no, pg.fecha,
                       pg.descripcion, pg.costo_tratamiento,
                       pg.cuota_inicial1, pg.cuota_inicial2, pg.cuota_inicial3, pg.cuota_inicial4,
                       pg.nocuotas, pg.ncuotas, pg.valor_cuota, pg.plan,
                       pg.cancelado, pg.fecha_inicio, pg.fecha_inicio2,
                       pg.cuota1, pg.cuota2, pg.cuota3, pg.cuota4, pg.cuota5, pg.cuota6,
                       pg.cuota7, pg.cuota8, pg.cuota9, pg.cuota10, pg.cuota11, pg.cuota12,
                       pg.cuota13, pg.cuota14, pg.cuota15, pg.cuota16, pg.cuota17, pg.cuota18,
                       pg.cuota19, pg.cuota20, pg.cuota21, pg.cuota22, pg.cuota23, pg.cuota24,
                       pg.cuota25, pg.cuota26, pg.cuota27, pg.cuota28, pg.cuota29, pg.cuota30,
                       pg.cuota31, pg.cuota32, pg.cuota33, pg.cuota34, pg.cuota35, pg.cuota36,
                       pg.paciente_paga_completo, pg.estado
                FROM pagos pg
                WHERE pg.paciente = :historia
                ORDER BY pg.fecha DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':historia', $historia);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findDetallesByPaciente(int $historia): array
    {
        $sql = "SELECT dp.ind, dp.observacion, dp.fecha, dp.tipo, dp.hora
                FROM detallespagos dp
                WHERE dp.paciente = :historia
                ORDER BY dp.fecha DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':historia', $historia);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
