<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Monolog\Logger;
use React\Http\Message\Response;
use React\Http\Message\ServerRequest;

final readonly class ServerLogger
{
    public function __construct(private Logger $logger) {}

    public function __invoke(ServerRequest $request, callable $next): Response
    {
        $this->logger->info(
            message: sprintf('%s %s', $request->getMethod(), $request->getUri()->getPath()),
            context: [
                'target' => $request->getRequestTarget(),
                'length' => $request->getBody()->getSize(),
            ],
        );

        return $next($request);
    }
}
