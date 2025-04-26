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
     * @param array<string, mixed> $payload
     * @throws \JsonException
     */
    protected function post(string $uri, array $payload): Response
    {
        return $this->request(new ServerRequest(
            method: Method::POST->name,
            url: $uri,
            headers: ['Content-Type' => 'application/json'],
            body: json_encode($payload, JSON_THROW_ON_ERROR),
        ));
    }

    protected function delete(string $uri): Response
    {
        return $this->request(new ServerRequest(
            method: Method::DELETE->name,
            url: $uri,
        ));
    }

    /**
     * @throws \JsonException
     */
    protected function assertIsJson(Response $response): HttpResponseAssertions
    {
        return new HttpResponseAssertions($response);
    }
}
