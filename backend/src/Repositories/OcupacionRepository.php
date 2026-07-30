<?php

declare(strict_types=1);

namespace Repositories;

use Config\Database;

class OcupacionRepository
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findByCodigo(string $codigo): ?array
    {
        try {
            $sql = "SELECT ind, codigo, nombre FROM ocupaciones WHERE codigo = :codigo LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':codigo', $codigo);
            $stmt->execute();
            $row = $stmt->fetch();
            return $row ?: null;
        } catch (\Throwable $e) {
            error_log('OcupacionRepository::findByCodigo error: ' . $e->getMessage());
            return null;
        }
    }

    public function search(string $search): array
    {
        try {
            $where = '';
            $params = [];
            if ($search !== '') {
                $where = "WHERE (nombre LIKE :s OR codigo LIKE :s2)";
                $params[':s'] = "%{$search}%";
                $params[':s2'] = "%{$search}%";
            }

            $sql = "SELECT ind, codigo, nombre
                    FROM ocupaciones {$where}
                    ORDER BY nombre ASC
                    LIMIT 100";
            $stmt = $this->db->prepare($sql);
            foreach ($params as $k => $v) {
                $stmt->bindValue($k, $v);
            }
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (\Throwable $e) {
            error_log('OcupacionRepository::search error: ' . $e->getMessage());
            return [];
        }
    }
}
