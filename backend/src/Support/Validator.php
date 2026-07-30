<?php

declare(strict_types=1);

namespace Support;

class Validator
{
    public static function int(mixed $value, int $default = 0): int
    {
        return (int)$value ?: $default;
    }

    public static function string(mixed $value, string $default = ''): string
    {
        return is_string($value) ? trim($value) : $default;
    }

    public static function date(mixed $value): ?string
    {
        if (empty($value)) {
            return null;
        }
        $d = \DateTime::createFromFormat('Y-m-d', (string)$value);
        return $d && $d->format('Y-m-d') === (string)$value ? $d->format('Y-m-d') : null;
    }

    public static function id(mixed $value): ?int
    {
        $v = (int)$value;
        return $v > 0 ? $v : null;
    }
}
