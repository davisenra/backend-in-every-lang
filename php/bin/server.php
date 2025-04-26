<?php

declare(strict_types=1);

use App\Definitions;
use DI\Container;
use Monolog\Logger;
use React\Http\HttpServer;
use React\Socket\SocketServer;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$container = new Container(Definitions::getDefinitions());
$logger = $container->get(Logger::class);
$server = $container->get(HttpServer::class);
$socket = new SocketServer('127.0.0.1:9997');
$server->listen($socket);
$logger->info('Server listening', ['address' => '127.0.0.1:9997']);
