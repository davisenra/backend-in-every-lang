<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\ResponseFactory;
use Monolog\Logger;
use React\Http\Message\Response;
use React\Http\Message\ServerRequest;

final readonly class HttpErrorHandler
{
    public function __construct(private Logger $logger) {}

    public function __invoke(ServerRequest $request, callable $next): Response
    {
        try {
            return $next($request);
        } catch (\Throwable $e) {
            $this->logger->error(
                message: 'Error while processing request',
                context: [
                    'exception' => $e,
                    'endpoint' => $request->getUri(),
                ],
            );
            return ResponseFactory::serverError(['error' => $e->getMessage()]);
        }
    }
}
