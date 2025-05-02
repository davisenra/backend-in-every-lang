<?php

declare(strict_types=1);

namespace App\Http;

use App\Encounters\Actions\DeleteEncounter;
use App\Encounters\Actions\ListEncounters;
use App\Encounters\Actions\UpdateEncounter;
use App\Species\Actions\DeleteSpecies;
use App\Species\Actions\ListSpecies;
use App\Encounters\Actions\RegisterEncounter;
use App\Encounters\Actions\ShowEncounter;
use App\Species\Actions\RegisterSpecies;
use App\Species\Actions\ShowSpecies;
use App\Species\Actions\UpdateSpecies;

final readonly class Routes
{
    public static function registerRoutes(Router $router): void
    {
        $router
            ->addRoute(Method::GET, '/healthcheck', Healthcheck::class)
            ->addRoute(Method::GET, '/encounters', ListEncounters::class)
            ->addRoute(Method::GET, '/encounters/{id:[0-9]+}', ShowEncounter::class)
            ->addRoute(Method::PATCH, '/encounters/{id:[0-9]+}', UpdateEncounter::class)
            ->addRoute(Method::DELETE, '/encounters/{id:[0-9]+}', DeleteEncounter::class)
            ->addRoute(Method::POST, '/encounters', RegisterEncounter::class)
            ->addRoute(Method::GET, '/species', ListSpecies::class)
            ->addRoute(Method::GET, '/species/{id:[0-9]+}', ShowSpecies::class)
            ->addRoute(Method::POST, '/species', RegisterSpecies::class)
            ->addRoute(Method::PATCH, '/species/{id:[0-9]+}', UpdateSpecies::class)
            ->addRoute(Method::DELETE, '/species/{id:[0-9]+}', DeleteSpecies::class);
    }
}
