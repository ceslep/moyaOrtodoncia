<?php

declare(strict_types=1);

namespace Repositories;

use Config\Database;

class CitaRepository
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findByPaciente(int $historia, ?string $estado, ?string $desde, ?string $hasta): array
    {
        $where = ['c.paciente = :historia'];
        $params[':historia'] = $historia;

        if ($estado !== null && $estado !== '') {
            if ($estado === 'asistio') {
                $where[] = "c.asistio = 'S'";
            } elseif ($estado === 'confirmo') {
                $where[] = "c.confirmo = 'S'";
            } elseif ($estado === 'cancelada') {
                $where[] = "c.asistio = 'N' AND c.borradopor IS NOT NULL";
            } elseif ($estado === 'pendiente') {
                $where[] = "(c.asistio IS NULL OR c.asistio = '')";
            }
        }
        if ($desde !== null) {
            $where[] = "c.fecha >= :desde";
            $params[':desde'] = $desde;
        }
        if ($hasta !== null) {
            $where[] = "c.fecha <= :hasta";
            $params[':hasta'] = $hasta;
        }

        $whereSql = implode(' AND ', $where);
        $sql = "SELECT c.ind, c.fecha, c.horas, c.paciente, c.procedimiento,
                       pr.nombre AS procedimiento_nombre,
                       c.especialista, c.asistio, c.confirmo,
                       c.tipo, c.motivo, c.duracion, c.enatencion,
                       c.anotaciones_cita, c.adicional_cita, c.adicional,
                       c.hora_llegada, c.hora_salida, c.proxima_cita,
                       c.reasignada, c.inicio, c.evolucion
                FROM citas c
                LEFT JOIN procedimientos pr ON c.procedimiento = pr.codigo
                WHERE {$whereSql}
                ORDER BY c.fecha DESC, c.vhoras ASC";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findCanceladasByPaciente(int $historia): array
    {
        $sql = "SELECT c.ind, c.fecha, c.horas, c.paciente, c.procedimiento,
                       pr.nombre AS procedimiento_nombre,
                       c.especialista, c.motivo,
                       c.borradopor, c.fechaborra, c.horaborra
                FROM canceladas c
                LEFT JOIN procedimientos pr ON c.procedimiento = pr.codigo
                WHERE c.paciente = :historia
                ORDER BY c.fechaborra DESC, c.horaborra DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':historia', $historia);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findGlobal(?string $desde, ?string $hasta, ?string $especialista, ?string $consultorio, ?string $estado, int $offset, int $perPage): array
    {
        $where = [];
        $params = [];

        if ($desde !== null) {
            $where[] = "c.fecha >= :desde";
            $params[':desde'] = $desde;
        }
        if ($hasta !== null) {
            $where[] = "c.fecha <= :hasta";
            $params[':hasta'] = $hasta;
        }
        if ($especialista !== null && $especialista !== '') {
            $where[] = "c.especialista LIKE :esp";
            $params[':esp'] = "%{$especialista}%";
        }
        if ($consultorio !== null && $consultorio !== '') {
            $where[] = "c.consultorio = :cons";
            $params[':cons'] = $consultorio;
        }
        if ($estado !== null && $estado !== '') {
            if ($estado === 'asistio') {
                $where[] = "c.asistio = 'S'";
            } elseif ($estado === 'confirmo') {
                $where[] = "c.confirmo = 'S'";
            } elseif ($estado === 'pendiente') {
                $where[] = "(c.asistio IS NULL OR c.asistio = '')";
            }
        }

        $whereSql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $countSql = "SELECT COUNT(*) FROM citas c {$whereSql}";
        $stmt = $this->db->prepare($countSql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();
        $total = (int)$stmt->fetchColumn();

        $sql = "SELECT c.ind, c.fecha, c.horas, c.paciente, c.procedimiento,
                       c.consultorio, c.especialista, c.asistio, c.confirmo,
                       c.tipo, c.motivo, c.duracion, c.enatencion,
                       c.hora_llegada, c.hora_salida, c.adicional_cita
                FROM citas c {$whereSql}
                ORDER BY c.fecha DESC, c.vhoras ASC
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
