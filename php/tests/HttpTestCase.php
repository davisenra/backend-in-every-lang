<?php

declare(strict_types=1);

namespace Tests;

use App\Definitions;
use App\Http\Method;
use App\Http\Router;
use DI\Container;
use PHPUnit\Framework\TestCase;
use React\Http\Message\Response;
use React\Http\Message\ServerRequest;

class HttpTestCase extends TestCase
{
    private Container $container;
    private Router $router;

    protected function setUp(): void
    {
        $this->container = new Container(Definitions::getDefinitions());
        $this->router = $this->container->get(Router::class);
    }

    protected function request(ServerRequest $request): Response
    {
        $router = $this->router;
        return $router($request);
    }

    protected function get(string $uri): Response
    {
        return $this->request(new ServerRequest(Method::GET->name, $uri));
    }

    /**
     * @throws \JsonException
     */
    protected function assertIsJson(Response $response): HttpResponseAssertions
    {
        return new HttpResponseAssertions($response);
    }
}
