<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Definitions;
use App\Http\Method;
use App\Http\Router;
use Clue\React\SQLite\DatabaseInterface;
use DI\Container;
use PHPUnit\Framework\TestCase;
use React\Http\Message\Response;
use React\Http\Message\ServerRequest;

use function React\Async\await;

abstract class ApplicationTestCase extends TestCase
{
    protected Container $container;
    protected Router $router;
    protected DatabaseInterface $database;

    protected function setUp(): void
    {
        $this->container = new Container(Definitions::getTestDefinitions());
        $this->database = $this->container->get(DatabaseInterface::class);
        $this->router = $this->container->get(Router::class);

        $this->runMigrations();
        $this->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->rollbackTransaction();
    }

    protected function request(ServerRequest $request): Response
    {
        $router = $this->router;
        return $router($request);
    }

    protected function get(string $uri): Response
    {
        $request = new ServerRequest(Method::GET->name, $uri);

        return $this->request($request);
    }

    /**
     * @param array<string, mixed> $payload
     * @throws \JsonException
     */
    protected function post(string $uri, array $payload): Response
    {
        $request = new ServerRequest(
            method: Method::POST->name,
            url: $uri,
            headers: ['Content-Type' => 'application/json'],
            body: json_encode($payload, JSON_THROW_ON_ERROR),
        );

        return $this->request($request);
    }

    protected function delete(string $uri): Response
    {
        $request = new ServerRequest(
            method: Method::DELETE->name,
            url: $uri,
        );

        return $this->request($request);
    }

    /**
     * @param array<string, mixed> $payload
     * @throws \JsonException
     */
    protected function patch(string $uri, array $payload): Response
    {
        $request = new ServerRequest(
            method: Method::PATCH->name,
            url: $uri,
            headers: ['Content-Type' => 'application/json'],
            body: json_encode($payload, JSON_THROW_ON_ERROR),
        );

        return $this->request($request);
    }

    /**
     * @throws \JsonException
     */
    protected function assertIsJson(Response $response): HttpResponseAssertions
    {
        return new HttpResponseAssertions($response);
    }

    private function runMigrations(): void
    {
        // this is blocking, but it's fine for now
        // ideally we should only run this once
        $migrations = file_get_contents(dirname(__DIR__) . '/../migrations/migrations.sql') ?: throw new \RuntimeException('Migrations not found');

        await($this->database->exec($migrations));
    }

    private function beginTransaction(): void
    {
        await($this->database->exec('BEGIN TRANSACTION'));
    }

    private function rollbackTransaction(): void
    {
        await($this->database->exec('ROLLBACK'));
    }
}
