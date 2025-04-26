<?php

declare(strict_types=1);

namespace App\Http;

use App\Actions\HttpAction;
use DI\Container;
use React\Http\Message\Response;
use React\Http\Message\ServerRequest;

final class Router
{
    /**
     * @var array<string, array<string, class-string<HttpAction>>> $routes
     */
    private array $routes = [];

    public function __construct(private readonly Container $container) {}

    public function __invoke(ServerRequest $request): Response
    {
        $method = Method::fromString($request->getMethod());
        $path = $request->getUri()->getPath();

        $actionClassString = $this->routes[$method->name][$path]
            ?? throw new \RuntimeException(
                "No route found for {$method->name} {$path}",
                404,
            );

        $action = $this->container->get($actionClassString);
        return $action($request);
    }

    /**
     * @param class-string<HttpAction> $actionClassString
     */
    public function addRoute(Method $method, string $path, string $actionClassString): self
    {
        if (!class_exists($actionClassString)) {
            throw new \InvalidArgumentException("Action class {$actionClassString} does not exist");
        }

        $reflection = new \ReflectionClass($actionClassString);
        if (!$reflection->implementsInterface(HttpAction::class)) {
            throw new \InvalidArgumentException(
                "Action class {$actionClassString} must implement HttpAction interface",
            );
        }

        $this->routes[$method->name][$path] = $actionClassString;

        return $this;
    }
}
