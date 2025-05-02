<?php

declare(strict_types=1);

namespace App;

use App\Http\Middleware\HttpErrorHandler;
use App\Http\Middleware\ServerLogger;
use App\Http\Router;
use App\Http\Routes;
use Clue\React\SQLite\DatabaseInterface;
use Clue\React\SQLite\Factory;
use DI\Container;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use CLosure;
use React\Http\HttpServer;
use React\Socket\SocketServer;
use Throwable;

final class Definitions
{
    /**
     * @return array<class-string|'config', Closure>
     */
    public static function getDefinitions(): array
    {
        return [

            'config' => function () {
                return [
                    'APP_HOST' => $_ENV['APP_HOST'] ?: '127.0.0.1',
                    'APP_PORT' => $_ENV['APP_PORT'] ?: '9997',
                    'DB_PATH' => $_ENV['DB_PATH'] ?: dirname(__DIR__) . '/database.db',
                ];
            },

            Router::class => function (Container $container): Router {
                $router = new Router($container);
                Routes::registerRoutes($router);

                return $router;
            },

            HttpServer::class => function (Container $container): HttpServer {
                $logger = $container->get(Logger::class);
                $middlewares = [
                    $container->get(HttpErrorHandler::class),
                    $container->get(ServerLogger::class),
                    $container->get(Router::class),
                ];
                $httpServer = new HttpServer(...$middlewares);
                $httpServer->on('error', fn(Throwable $error) => $logger->error($error));

                return $httpServer;
            },

            SocketServer::class => function (Container $container): SocketServer {
                $appConfig = $container->get('config');
                $serverHost = sprintf('%s:%s', $appConfig['APP_HOST'], $appConfig['APP_PORT']);

                return new SocketServer($serverHost);
            },

            DatabaseInterface::class => function (Container $container): DatabaseInterface {
                $dbPath = $container->get('config')['DB_PATH'];

                return new Factory()->openLazy($dbPath);
            },

            Logger::class => fn() => new Logger('app', [new StreamHandler(STDOUT)]),

        ];
    }

    /**
     * @return array<class-string|'config', Closure>
     */
    public static function getTestDefinitions(): array
    {
        $definitions = Definitions::getDefinitions();

        $definitions[DatabaseInterface::class] = fn() => new Factory()->openLazy(':memory:');

        return $definitions;
    }
}
