<?php

declare(strict_types=1);

namespace App;

use App\Actions\ListEncounters;
use App\Actions\ListSpecies;
use App\Http\Method;
use App\Http\Middleware\HttpErrorHandler;
use App\Http\Middleware\ServerLogger;
use App\Http\Router;
use Clue\React\SQLite\DatabaseInterface;
use Clue\React\SQLite\Factory;
use DI\Container;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use CLosure;
use React\Http\HttpServer;
use Throwable;

final class Definitions
{
    /**
     * @return array<class-string, Closure>
     */
    public static function getDefinitions(): array
    {
        return [

            Router::class => function (Container $container): Router {
                return new Router($container)
                    ->addRoute(Method::GET, '/encounters', ListEncounters::class)
                    ->addRoute(Method::GET, '/species', ListSpecies::class);
            },

            HttpServer::class => function (Container $container): HttpServer {
                $logger = $container->get(Logger::class);

                $httpServer = new HttpServer(
                    $container->get(HttpErrorHandler::class),
                    $container->get(ServerLogger::class),
                    $container->get(Router::class),
                );

                $httpServer->on('error', fn(Throwable $error) => $logger->error($error));

                return $httpServer;
            },

            DatabaseInterface::class => function (): DatabaseInterface {
                return new Factory()->openLazy(dirname(__DIR__) . '/database.db');
            },

            Logger::class => function (): Logger {
                return new Logger('app', [new StreamHandler(STDOUT)]);
            },

        ];
    }
}
