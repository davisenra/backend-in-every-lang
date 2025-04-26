<?php

namespace App\Actions;

use React\Http\Message\Response;
use React\Http\Message\ServerRequest;

interface HttpAction
{
    public function __invoke(ServerRequest $request): Response;
}
