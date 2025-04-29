<?php

declare(strict_types=1);

use App\Definitions;
use DI\Container;
use Monolog\Logger;
use React\Http\HttpServer;
use React\Socket\SocketServer;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$container = new Container(Definitions::getDefinitions());
$config = $container->get('config');
$logger = $container->get(Logger::class);
$socket = $container->get(SocketServer::class);
$server = $container->get(HttpServer::class);
$server->listen($socket);
$logger->info(
    message: 'Server listening',
    context: [
        'address' => sprintf('http://%s:%s', $config['APP_HOST'], $config['APP_PORT']),
    ],
);
