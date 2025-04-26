<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Fig\Http\Message\StatusCodeInterface;
use React\Http\Message\Response;
use React\Http\Message\ServerRequest;

final readonly class HttpErrorHandler
{
    public function __invoke(ServerRequest $request, callable $next): Response
    {
        try {
            return $next($request);
        } catch (\Throwable $e) {
            return new Response(
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                [
                    'Content-Type' => 'application/json; charset=utf-8',
                ],
                json_encode([
                    'error' => $e->getMessage(),
                ], JSON_THROW_ON_ERROR),
            );
        }
    }
}
