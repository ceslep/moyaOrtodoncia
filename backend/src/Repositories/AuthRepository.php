<?php

declare(strict_types=1);

namespace Repositories;

use Config\Database;

class AuthRepository
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findByUsuario(string $usuario): ?array
    {
        $stmt = $this->db->prepare('SELECT id, usuario, password FROM usuarios WHERE usuario = :u LIMIT 1');
        $stmt->execute([':u' => $usuario]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function createToken(int $userId): string
    {
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

        $stmt = $this->db->prepare(
            'INSERT INTO user_tokens (user_id, token, expires_at) VALUES (:uid, :t, :exp)'
        );
        $stmt->execute([
            ':uid' => $userId,
            ':t'   => $token,
            ':exp' => $expiresAt,
        ]);

        return $token;
    }

    public function validateToken(string $token): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT ut.user_id, u.usuario
             FROM user_tokens ut
             JOIN usuarios u ON u.id = ut.user_id
             WHERE ut.token = :t AND ut.expires_at > NOW()
             LIMIT 1'
        );
        $stmt->execute([':t' => $token]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function deleteToken(string $token): void
    {
        $stmt = $this->db->prepare('DELETE FROM user_tokens WHERE token = :t');
        $stmt->execute([':t' => $token]);
    }

    public function deleteExpiredTokens(): void
    {
        $this->db->exec('DELETE FROM user_tokens WHERE expires_at <= NOW()');
    }
}
