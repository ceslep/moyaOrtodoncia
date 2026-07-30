<?php

declare(strict_types=1);

namespace Config;

class Database
{
    private static ?Database $instance = null;
    private \PDO $pdo;

    private function __construct()
    {
        $env = $this->loadEnv();
        $host = $env['DB_HOST'] ?? 'localhost';
        $name = $env['DB_NAME'] ?? 'iedeocci_adm';
        $user = $env['DB_USER'] ?? 'iedeocci_adm';
        $pass = $env['DB_PASS'] ?? '';
        $charset = $env['DB_CHARSET'] ?? 'utf8mb4';
        $port = $env['DB_PORT'] ?? '3306';

        $dsn = "mysql:host={$host};port={$port};dbname={$name};charset={$charset}";
        $this->pdo = new \PDO($dsn, $user, $pass, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): \PDO
    {
        return $this->pdo;
    }

    private function loadEnv(): array
    {
        $path = dirname(__DIR__, 2) . '/.env';
        $vars = [];
        if (!file_exists($path)) {
            return $vars;
        }
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#') {
                continue;
            }
            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $val = trim($parts[1]);
                $vars[$key] = $val;
            }
        }
        return $vars;
    }
}
