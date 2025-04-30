<?php

declare(strict_types=1);

namespace App\Http;

use React\Http\Message\Response;
use React\Http\Message\ServerRequest;

final readonly class Healthcheck implements HttpAction
{
    public function __invoke(ServerRequest $request): Response
    {
        return new Response(200);
    }
}
