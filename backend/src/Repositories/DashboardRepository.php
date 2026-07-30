<?php

declare(strict_types=1);

namespace Repositories;

use Config\Database;

class DashboardRepository
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function resumen(): array
    {
        $hoy = date('Y-m-d');
        $manana = date('Y-m-d', strtotime('+1 day'));
        $semanaInicio = date('Y-m-d', strtotime('monday this week'));
        $semanaFin = date('Y-m-d', strtotime('sunday this week'));

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM paciente WHERE estado = 'ACTIVO'");
        $stmt->execute();
        $pacientesActivos = (int)$stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM citas WHERE fecha = :hoy");
        $stmt->bindValue(':hoy', $hoy);
        $stmt->execute();
        $citasHoy = (int)$stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM citas WHERE fecha >= :ini AND fecha <= :fin");
        $stmt->bindValue(':ini', $semanaInicio);
        $stmt->bindValue(':fin', $semanaFin);
        $stmt->execute();
        $citasSemana = (int)$stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM citas WHERE fecha = :manana");
        $stmt->bindValue(':manana', $manana);
        $stmt->execute();
        $citasManana = (int)$stmt->fetchColumn();

        $stmt = $this->db->query("SELECT COALESCE(SUM(saldo), 0) FROM paciente WHERE saldo > 0 AND estado = 'ACTIVO'");
        $carteraPendiente = (float)$stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT COALESCE(SUM(valor_abono), 0) FROM abonos WHERE fecha = :hoy");
        $stmt->bindValue(':hoy', $hoy);
        $stmt->execute();
        $abonosHoy = (float)$stmt->fetchColumn();

        $stmt = $this->db->query("SELECT COUNT(*) FROM paciente WHERE estado = 'ACTIVO' AND MONTH(fecha_inicio) = MONTH(CURDATE()) AND YEAR(fecha_inicio) = YEAR(CURDATE())");
        $nuevosMes = (int)$stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT c.fecha, c.horas, c.procedimiento, c.especialista, c.asistio, c.confirmo,
                                            p.nombres, p.historia
                                     FROM citas c
                                     JOIN paciente p ON CAST(c.paciente AS UNSIGNED) = p.historia
                                     WHERE c.fecha >= :hoy
                                     ORDER BY c.fecha ASC, c.vhoras ASC
                                     LIMIT 10");
        $stmt->bindValue(':hoy', $hoy);
        $stmt->execute();
        $proximasCitas = $stmt->fetchAll();

        return [
            'pacientes_activos' => $pacientesActivos,
            'citas_hoy' => $citasHoy,
            'citas_semana' => $citasSemana,
            'citas_manana' => $citasManana,
            'cartera_pendiente' => $carteraPendiente,
            'abonos_hoy' => $abonosHoy,
            'nuevos_mes' => $nuevosMes,
            'proximas_citas' => $proximasCitas,
        ];
    }

    public function datosEmpresa(): ?array
    {
        $sql = "SELECT ind, nit, nombre_empresa, ciudad, direccion, telefono,
                       fax, email, web, representante_legal, especialidad, sede,
                       CASE WHEN logo IS NOT NULL THEN 1 ELSE 0 END as tiene_logo
                FROM datos_empresa LIMIT 1";
        $stmt = $this->db->query($sql);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
