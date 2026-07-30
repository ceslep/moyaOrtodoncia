<?php

declare(strict_types=1);

namespace Repositories;

use Config\Database;

class AbonoRepository
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findByPaciente(int $historia): array
    {
        $sql = "SELECT a.ind, a.paciente, a.identificacion, a.recibo,
                       a.valor_abono, a.fecha, a.hora, a.forma_de_pago,
                       a.cheque, a.banco, a.detalle, a.concita,
                       a.acentado_por, a.doctor, a.tipo, a.tipo_pago, a.total
                FROM abonos a
                WHERE a.paciente = :historia
                ORDER BY a.fecha DESC, a.hora DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':historia', $historia);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findGlobal(?string $desde, ?string $hasta, ?string $formaPago, int $offset, int $perPage): array
    {
        $where = [];
        $params = [];

        if ($desde !== null) {
            $where[] = "a.fecha >= :desde";
            $params[':desde'] = $desde;
        }
        if ($hasta !== null) {
            $where[] = "a.fecha <= :hasta";
            $params[':hasta'] = $hasta;
        }
        if ($formaPago !== null && $formaPago !== '') {
            $where[] = "a.forma_de_pago = :fp";
            $params[':fp'] = $formaPago;
        }

        $whereSql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $countSql = "SELECT COUNT(*) FROM abonos a {$whereSql}";
        $stmt = $this->db->prepare($countSql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();
        $total = (int)$stmt->fetchColumn();

        $sql = "SELECT a.ind, a.paciente, a.identificacion, a.recibo,
                       a.valor_abono, a.fecha, a.hora, a.forma_de_pago,
                       a.cheque, a.banco, a.detalle,
                       a.acentado_por, a.doctor, a.tipo, a.total
                FROM abonos a {$whereSql}
                ORDER BY a.fecha DESC, a.hora DESC
                LIMIT :offset, :limit";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->execute();

        return ['data' => $stmt->fetchAll(), 'total' => $total];
    }
}
