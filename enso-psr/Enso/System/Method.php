<?php
declare(strict_types = 1);

namespace Enso\System;

enum Method: string
{
    case HEAD = 'HEAD';
    case OPTIONS = 'OPTIONS';
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case PATCH = 'PATCH';
    case DELETE = 'DELETE';

    public static function fromString(string $method): ?self
    {
        return self::tryFrom(strtoupper($method));
    }

    public static function values(): array
    {
        return array_map(fn (self $e) => $e->value, self::cases());
    }
}