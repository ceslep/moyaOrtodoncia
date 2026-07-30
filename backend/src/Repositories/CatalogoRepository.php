<?php

declare(strict_types=1);

namespace Repositories;

use Config\Database;

class CatalogoRepository
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function procedimientos(string $search, int $offset, int $perPage): array
    {
        $where = '';
        $params = [];
        if ($search !== '') {
            $where = "WHERE (nombre LIKE :s OR codigo LIKE :s2 OR tipocita LIKE :s3)";
            $params[':s'] = "%{$search}%";
            $params[':s2'] = "%{$search}%";
            $params[':s3'] = "%{$search}%";
        }

        $countSql = "SELECT COUNT(*) FROM procedimientos {$where}";
        $stmt = $this->db->prepare($countSql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();
        $total = (int)$stmt->fetchColumn();

        $sql = "SELECT ind, codigo, nombre, duracion, color, tipocita, etapa, tipoconsulta, tipoprocedimiento
                FROM procedimientos {$where}
                ORDER BY nombre ASC
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

    public function especialidades(string $search): array
    {
        $where = '';
        $params = [];
        if ($search !== '') {
            $where = "WHERE (nombre LIKE :s OR descripcion LIKE :s2 OR codigo LIKE :s3)";
            $params[':s'] = "%{$search}%";
            $params[':s2'] = "%{$search}%";
            $params[':s3'] = "%{$search}%";
        }

        $sql = "SELECT ind, nombre, descripcion, codigo, activa, grupo, abreviatura
                FROM especialidades {$where}
                ORDER BY nombre ASC";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function entidades(string $search): array
    {
        $where = '';
        $params = [];
        if ($search !== '') {
            $where = "WHERE (nombres LIKE :s OR nocodigo LIKE :s2 OR nit LIKE :s3)";
            $params[':s'] = "%{$search}%";
            $params[':s2'] = "%{$search}%";
            $params[':s3'] = "%{$search}%";
        }

        $sql = "SELECT ind, nit, nocodigo, nombres, direccion, ciudad, telefono, email
                FROM entidades {$where}
                ORDER BY nombres ASC";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
