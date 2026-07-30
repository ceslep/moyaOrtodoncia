<?php

declare(strict_types=1);

namespace Repositories;

use Config\Database;

class EvolucionRepository
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findByPaciente(int $historia): array
    {
        $sql = "SELECT e.ind, e.paciente, e.fecha, e.hora, e.procedimiento,
                       e.mevolucion, e.diagnostico_dental, e.diagnostico_principal,
                       e.diagnostico_relacionado1, e.diagnostico_relacionado2,
                       e.valor_consulta, e.valor_copago, e.neto,
                       e.diagnostico_pulpar, e.causa_externa,
                       e.tipoprocedimiento, e.ambito, e.finalidad,
                       e.personal_que_atiende, e.valorproc,
                       e.factura_consulta, e.autofactura,
                       e.auxiliar, e.profesional,
                       e.d11, e.d11_text, e.d12, e.d12_text, e.d13, e.d13_text,
                       e.d14, e.d14_text, e.d15, e.d15_text, e.d16, e.d16_text,
                       e.d17, e.d17_text, e.d18, e.d18_text,
                       e.d21, e.d21_text, e.d22, e.d22_text, e.d23, e.d23_text,
                       e.d24, e.d24_text, e.d25, e.d25_text, e.d26, e.d26_text,
                       e.d27, e.d27_text, e.d28, e.d28_text,
                       e.d31, e.d31_text, e.d32, e.d32_text, e.d33, e.d33_text,
                       e.d34, e.d34_text, e.d35, e.d35_text, e.d36, e.d36_text,
                       e.d37, e.d37_text, e.d38, e.d38_text,
                       e.d41, e.d41_text, e.d42, e.d42_text, e.d43, e.d43_text,
                       e.d44, e.d44_text, e.d45, e.d45_text, e.d46, e.d46_text,
                       e.d47, e.d47_text, e.d48, e.d48_text,
                       e.d51, e.d51_text, e.d52, e.d52_text, e.d53, e.d53_text,
                       e.d54, e.d54_text, e.d55, e.d55_text,
                       e.d61, e.d61_text, e.d62, e.d62_text, e.d63, e.d63_text,
                       e.d64, e.d64_text, e.d65, e.d65_text,
                       e.d71, e.d71_text, e.d72, e.d72_text, e.d73, e.d73_text,
                       e.d74, e.d74_text, e.d75, e.d75_text,
                       e.d81, e.d81_text, e.d82, e.d82_text, e.d83, e.d83_text,
                       e.d84, e.d84_text, e.d85, e.d85_text
                FROM evolucion e
                WHERE e.paciente = :historia
                ORDER BY e.fecha DESC, e.hora DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':historia', $historia);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
