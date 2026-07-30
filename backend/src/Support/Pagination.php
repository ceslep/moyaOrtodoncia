<?php

declare(strict_types=1);

namespace Support;

class Pagination
{
    public static function params(): array
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = max(1, min(100, (int)($_GET['per_page'] ?? 20)));
        $offset = ($page - 1) * $perPage;
        return ['page' => $page, 'per_page' => $perPage, 'offset' => $offset];
    }
}
