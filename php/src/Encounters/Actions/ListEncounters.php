<?php

declare(strict_types=1);

namespace App\Encounters\Actions;

use App\Http\HttpAction;
use App\Http\ResponseFactory;
use App\Encounters\Encounter;
use Clue\React\SQLite\DatabaseInterface;
use Clue\React\SQLite\Result;
use React\Http\Message\Response;
use React\Http\Message\ServerRequest;

use function React\Async\await;

final readonly class ListEncounters implements HttpAction
{
    public function __construct(private DatabaseInterface $db) {}

    public function __invoke(ServerRequest $request): Response
    {
        /** @var Result $result */
        $result = await($this->db->query($this->getQuery()));
        $encounters = array_map(fn($r) => Encounter::fromDatabaseRow($r), $result->rows);

        return ResponseFactory::ok(['encounters' => $encounters]);
    }

    private function getQuery(): string
    {
        return <<<SQL
            SELECT
                e.id as "id",
                e.location as "location",
                e.description as "description",
                e.species_id as "species_id"
            FROM encounters e
            ORDER BY e.id DESC
        SQL;
    }
}
