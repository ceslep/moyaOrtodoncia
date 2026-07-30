<?php

declare(strict_types=1);

namespace Repositories;

use Config\Database;

class PacienteRepository
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function search(string $search, int $offset, int $perPage): array
    {
        $where = '';
        $params = [];
        if ($search !== '') {
            $where = "WHERE (p.identificacion LIKE :s OR p.historia LIKE :s2 OR p.nombres LIKE :s3 OR p.apellido1 LIKE :s4 OR p.apellido2 LIKE :s5 OR p.nombre1 LIKE :s6)";
            $params[':s'] = "%{$search}%";
            $params[':s2'] = "%{$search}%";
            $params[':s3'] = "%{$search}%";
            $params[':s4'] = "%{$search}%";
            $params[':s5'] = "%{$search}%";
            $params[':s6'] = "%{$search}%";
        }

        $countSql = "SELECT COUNT(*) FROM paciente p {$where}";
        $stmt = $this->db->prepare($countSql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();
        $total = (int)$stmt->fetchColumn();

        $sql = "SELECT p.ind, p.historia, p.identificacion, p.nombres, p.nombre1, p.nombre2,
                       p.apellido1, p.apellido2, p.estado, p.telefono_movil, p.telefono_residencia1,
                       p.saldo, p.fecnac, p.edad, p.sexo, p.email1, p.fecha_inicio,
                       p.direccion_residencia, p.ciudad_residencia,
                       CASE WHEN p.foto IS NOT NULL THEN 1 ELSE 0 END as tiene_foto
                FROM paciente p {$where}
                ORDER BY p.ind DESC
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

    public function findById(int $ind): ?array
    {
        $sql = "SELECT p.ind, p.historia, p.identificacion, p.fecha, p.tdei,
                       p.nombres, p.nombre1, p.nombre2, p.apellido1, p.apellido2,
                       p.fecnac, p.edad, p.sexo, p.estado,
                       p.direccion_residencia, p.ciudad_residencia, p.barrio,
                       p.telefono_residencia1, p.telefono_residencia2, p.telefono_movil,
                       p.email1, p.email2, p.ocupacion, p.estado_civil,
                       p.lugarnacimiento, p.nivel_educativo, p.en_que_colegio,
                       p.saldo, p.fecha_inicio, p.fecha_inicio2,
                       p.costo_tratamiento, p.cuota_inicial1, p.cuota_inicial2,
                       p.cuota_inicial3, p.cuota_inicial4,
                       p.nocuotas, p.ncuotas, p.valor_cuota, p.plan, p.modalidad_de_pago,
                       p.paciente_paga_completo,
                       p.nombre_padre, p.telefono_padre, p.identificacion_padre, p.movil_padre, p.ocupacion_padre,
                       p.nombre_madre, p.telefono_madre, p.identificacion_madre, p.movil_madre, p.ocupacion_madre,
                       p.nombre_acudiente, p.telefono_acudiente, p.identificacion_acudiente, p.movil_acudiente, p.ocupacion_acudiente,
                       p.email_padre, p.email_madre, p.email_acudiente,
                       p.nombre_conyuge, p.telefono_conyuge, p.empresa_conyuge, p.telefono_empresa_conyuge,
                       p.cantidad_hermanos, p.casa_propia, p.casa_arrendada,
                       p.observaciones, p.observacion_especial,
                       p.padece, p.cual, p.recibe_medicamento, p.cual_medicamento,
                       p.padecimientos, p.observaciones_medicas,
                       p.habitos, p.otros_habitos, p.cepilla, p.usa_seda,
                       p.denticion_permanente, p.denticion_mixta,
                       p.relacion_canina, p.relacion_molar,
                       p.overjet, p.overbite, p.mordida_abierta, p.mordida_cruzada,
                       p.diastemas, p.dientes_ausentes, p.manchas_dentales, p.fracturas,
                       p.higiene_oral, p.caries, p.peridonto,
                       p.disfuncion_articular, p.otros_hallazgos,
                       p.diagnostico_medico_general, p.diagnostico_intraoral,
                       p.diagnostico_dental, p.diagnostico_periodontal, p.diagnostico_endodontico,
                       p.plan_tratamiento, p.plan_de_tratamiento, p.remisiones_odontologicas,
                       p.previo, p.tiempo_ortodoncia,
                       p.deportes, p.reds, p.hobbies,
                       p.como_supo_de_nosotros, p.remitido_por, p.telefono_remitido,
                       p.odontologo_personal, p.telefono_odontologo,
                       p.tipo, p.profesional, p.razon_inicia,
                       p.terminado, p.retencion, p.consecuencia,
                       p.entidad, p.tipo_de_usuario, p.nivel_sisben,
                       CASE WHEN p.foto IS NOT NULL THEN 1 ELSE 0 END as tiene_foto
                FROM paciente p WHERE p.ind = :ind";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':ind', $ind, \PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findFoto(int $ind): ?string
    {
        $sql = "SELECT foto FROM paciente WHERE ind = :ind AND foto IS NOT NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':ind', $ind, \PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_NUM);
        return $row ? $row[0] : null;
    }

    public function getHistoria(int $ind): ?array
    {
        $sql = "SELECT p.historia FROM paciente p WHERE p.ind = :ind";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':ind', $ind, \PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_NUM);
        return $row ? ['historia' => (int)$row[0]] : null;
    }

    public function getCuotas(int $ind): ?array
    {
        $sql = "SELECT p.cuota1, p.cuota2, p.cuota3, p.cuota4, p.cuota5, p.cuota6,
                       p.cuota7, p.cuota8, p.cuota9, p.cuota10, p.cuota11, p.cuota12,
                       p.cuota13, p.cuota14, p.cuota15, p.cuota16, p.cuota17, p.cuota18,
                       p.cuota19, p.cuota20, p.cuota21, p.cuota22, p.cuota23, p.cuota24,
                       p.cuota25, p.cuota26, p.cuota27, p.cuota28, p.cuota29, p.cuota30,
                       p.cuota31, p.cuota32, p.cuota33, p.cuota34, p.cuota35, p.cuota36
                FROM paciente p WHERE p.ind = :ind";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':ind', $ind, \PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
