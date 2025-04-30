<?php

declare(strict_types=1);

namespace App\Http;

use App\Encounters\Actions\DeleteEncounter;
use App\Encounters\Actions\ListEncounters;
use App\Species\Actions\ListSpecies;
use App\Encounters\Actions\RegisterEncounter;
use App\Encounters\Actions\ShowEncounter;
use App\Species\Actions\ShowSpecies;

final readonly class Routes
{
    public static function registerRoutes(Router $router): void
    {
        $router
            ->addRoute(Method::GET, '/healthcheck', Healthcheck::class)
            ->addRoute(Method::GET, '/encounters', ListEncounters::class)
            ->addRoute(Method::GET, '/encounters/{id:[0-9]+}', ShowEncounter::class)
            ->addRoute(Method::DELETE, '/encounters/{id:[0-9]+}', DeleteEncounter::class)
            ->addRoute(Method::POST, '/encounters', RegisterEncounter::class)
            ->addRoute(Method::GET, '/species', ListSpecies::class)
            ->addRoute(Method::GET, '/species/{id:[0-9]+}', ShowSpecies::class);
    }
}
