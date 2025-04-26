<?php

declare(strict_types=1);

namespace App\Http;

enum Method
{
    case GET;
    case POST;
    case PUT;
    case PATCH;
    case DELETE;
    case HEAD;
    case OPTIONS;
    case CONNECT;
    case TRACE;

    public static function fromString(string $requestMethod): self
    {
        return match (strtoupper($requestMethod)) {
            'POST' => self::POST,
            'PUT' => self::PUT,
            'PATCH' => self::PATCH,
            'DELETE' => self::DELETE,
            'HEAD' => self::HEAD,
            'OPTIONS' => self::OPTIONS,
            'CONNECT' => self::CONNECT,
            'TRACE' => self::TRACE,
            default => self::GET,
        };
    }
}
