<?php

declare(strict_types=1);

namespace Repositories;

use Config\Database;

class HojaVidaRepository
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
            $where = "WHERE (hv.nombres LIKE :s OR hv.apellidos LIKE :s2 OR hv.identificacion LIKE :s3 OR hv.especialidad LIKE :s4)";
            $params[':s'] = "%{$search}%";
            $params[':s2'] = "%{$search}%";
            $params[':s3'] = "%{$search}%";
            $params[':s4'] = "%{$search}%";
        }

        $countSql = "SELECT COUNT(*) FROM hoja_vida hv {$where}";
        $stmt = $this->db->prepare($countSql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();
        $total = (int)$stmt->fetchColumn();

        $sql = "SELECT hv.ind, hv.identificacion, hv.nombres, hv.apellidos,
                       hv.fecha, hv.edad, hv.especialidad, hv.telefono, hv.email,
                       hv.estado, hv.ciudad, hv.residencia,
                       hv.estadocivil, hv.activo, hv.tipo,
                       hv.nombresp, hv.tarjeta_profesional, hv.otorgadopor,
                       CASE WHEN hv.foto IS NOT NULL THEN 1 ELSE 0 END as tiene_foto
                FROM hoja_vida hv {$where}
                ORDER BY hv.apellidos ASC, hv.nombres ASC
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

    public function findByIdentificacion(int $ind, int $identificacion): ?array
    {
        $sql = "SELECT hv.ind, hv.identificacion, hv.nombres, hv.apellidos,
                       hv.fecha, hv.edad, hv.especialidad, hv.telefono, hv.email,
                       hv.estado, hv.ciudad, hv.residencia, hv.estadocivil,
                       hv.activo, hv.tipo, hv.nombresp, hv.tarjeta_profesional, hv.otorgadopor,
                       hv.estprimaria, hv.priciudad, hv.apri, hv.titpri,
                       hv.estsecundaria, hv.secciudad, hv.asec, hv.titsec,
                       hv.auniv, hv.tituniv, hv.estuniversidad, hv.unciudad,
                       hv.postgradoestab, hv.postciudad, hv.postanio, hv.posttitulo,
                       hv.otrosestab, hv.otrosciudad, hv.otrosanio, hv.otrostitulo,
                       hv.expemp1, hv.expcargo1, hv.expfechinicio1, hv.expfechretiro1, hv.expfuncrealizadas1,
                       hv.expemp2, hv.expcargo2, hv.expfechinicio2, hv.expfechretiro2, hv.expfuncrealizadas2,
                       hv.expemp3, hv.expcargo3, hv.expfechinicio3, hv.expfechretiro3, hv.expfuncrealizadas3,
                       hv.npadres1, hv.profesionpadre1, hv.direccionpadre1, hv.telefono1padre1,
                       hv.npadres2, hv.profesionpadre2, hv.direccionpadre2, hv.telefono1padre2,
                       hv.nesposo, hv.profesionesposo, hv.direccionesposo, hv.telefono1esposo,
                       hv.salud, hv.pension, hv.riesgosp, hv.tieneconfamiliar,
                       hv.nultimopatrono, hv.bitacora,
                       CASE WHEN hv.foto IS NOT NULL THEN 1 ELSE 0 END as tiene_foto
                FROM hoja_vida hv
                WHERE hv.ind = :ind AND hv.identificacion = :identificacion";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':ind', $ind, \PDO::PARAM_INT);
        $stmt->bindValue(':identificacion', $identificacion, \PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findFoto(int $ind, int $identificacion): ?string
    {
        $sql = "SELECT foto FROM hoja_vida WHERE ind = :ind AND identificacion = :identificacion AND foto IS NOT NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':ind', $ind, \PDO::PARAM_INT);
        $stmt->bindValue(':identificacion', $identificacion, \PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_NUM);
        return $row ? $row[0] : null;
    }
}
