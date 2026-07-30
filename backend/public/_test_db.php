<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/src/Config/Database.php';

use Config\Database;

$db = Database::getInstance()->getConnection();

// List tables with 'municipio' or 'ocupacion' in name
$stmt = $db->query("SHOW TABLES LIKE '%municipio%'");
$municipios = $stmt->fetchAll();

$stmt = $db->query("SHOW TABLES LIKE '%ocupacion%'");
$ocupaciones = $stmt->fetchAll();

$stmt = $db->query("SHOW TABLES LIKE '%ciudad%'");
$ciudades = $stmt->fetchAll();

echo "=== Tablas Municipio ===\n";
print_r($municipios);

echo "\n=== Tablas Ocupacion ===\n";
print_r($ocupaciones);

echo "\n=== Tablas Ciudad ===\n";
print_r($ciudades);

// Show columns for found tables
foreach ($municipios as $row) {
    $table = $row[0];
    echo "\n=== Columnas: {$table} ===\n";
    $stmt = $db->query("DESCRIBE {$table}");
    print_r($stmt->fetchAll());
    $stmt = $db->query("SELECT * FROM {$table} LIMIT 3");
    print_r($stmt->fetchAll());
}

foreach ($ocupaciones as $row) {
    $table = $row[0];
    echo "\n=== Columnas: {$table} ===\n";
    $stmt = $db->query("DESCRIBE {$table}");
    print_r($stmt->fetchAll());
    $stmt = $db->query("SELECT * FROM {$table} LIMIT 3");
    print_r($stmt->fetchAll());
}

foreach ($ciudades as $row) {
    $table = $row[0];
    echo "\n=== Columnas: {$table} ===\n";
    $stmt = $db->query("DESCRIBE {$table}");
    print_r($stmt->fetchAll());
    $stmt = $db->query("SELECT * FROM {$table} LIMIT 3");
    print_r($stmt->fetchAll());
}
