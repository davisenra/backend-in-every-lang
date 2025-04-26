<?php

declare(strict_types=1);

namespace App;

use App\Actions\ListEncounters;
use App\Actions\ListSpecies;
use App\Actions\ShowEncounter;
use App\Actions\ShowSpecies;
use App\Http\Method;
use App\Http\Router;

final readonly class Routes
{
    public static function registerRoutes(Router $router): void
    {
        $router
            ->addRoute(Method::GET, '/encounters', ListEncounters::class)
            ->addRoute(Method::GET, '/encounters/{id:[0-9]+}', ShowEncounter::class)
            ->addRoute(Method::GET, '/species', ListSpecies::class)
            ->addRoute(Method::GET, '/species/{id:[0-9]+}', ShowSpecies::class);
    }
}
