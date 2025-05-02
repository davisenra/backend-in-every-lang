<?php

declare(strict_types=1);

namespace App\Http;

use DI\Container;
use React\Http\Message\Response;
use React\Http\Message\ServerRequest;

final class Router
{
    /**
     * @var array<string, array<string, array{action: class-string<HttpAction>, regex: string, params: array<string> }>> $routes
     */
    private array $routes = [];

    public function __construct(private readonly Container $container) {}

    public function __invoke(ServerRequest $request): Response
    {
        $method = Method::fromString($request->getMethod());
        $path = $request->getUri()->getPath();

        foreach ($this->routes[$method->name] ?? [] as $routeData) {
            if (preg_match($routeData['regex'], $path, $matches)) {
                $params = [];
                foreach ($routeData['params'] as $index => $paramName) {
                    if (isset($matches[$index + 1])) {
                        $params[$paramName] = $matches[$index + 1];
                    }
                }

                $action = $this->container->get($routeData['action']);
                return $action($request->withAttribute('routeParams', $params));
            }
        }

        return new Response(404);
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

        $paramPatterns = [];
        preg_match_all('/{([^:}]+)(?::([^}]+))?}/', $path, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $fullMatch = $match[0];
            $paramName = $match[1];
            $paramPattern = $match[2] ?? '[^/]+';

            $paramPatterns[$fullMatch] = [
                'name' => $paramName,
                'pattern' => $paramPattern,
            ];
        }

        // first, escape the full path for use in a regex
        $pattern = preg_quote($path, '/');

        // then replace the parameter placeholders with their regex patterns
        $paramNames = [];
        foreach ($paramPatterns as $placeholder => $data) {
            $escapedPlaceholder = preg_quote($placeholder, '/');
            $paramNames[] = $data['name'];
            $pattern = str_replace($escapedPlaceholder, '(' . $data['pattern'] . ')', $pattern);
        }

        $regex = '/^' . $pattern . '$/';

        $this->routes[$method->name][$path] = [
            'action' => $actionClassString,
            'regex' => $regex,
            'params' => $paramNames,
        ];

        return $this;
    }
}
