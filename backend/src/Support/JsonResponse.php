<?php

declare(strict_types=1);

namespace Support;

class JsonResponse
{
    public static function success(array $data, array $meta = []): void
    {
        header('Content-Type: application/json; charset=utf-8');
        $response = ['data' => $data];
        if (!empty($meta)) {
            $response['meta'] = $meta;
        }
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function paginated(array $data, int $page, int $perPage, int $total): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'data' => $data,
            'meta' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => (int)ceil($total / $perPage),
            ],
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function error(string $message, int $code = 400): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'error' => [
                'message' => $message,
                'code' => $code,
            ],
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function notFound(string $message = 'Recurso no encontrado'): void
    {
        self::error($message, 404);
    }
}
