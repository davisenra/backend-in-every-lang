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

final readonly class ShowEncounter implements HttpAction
{
    public function __construct(private DatabaseInterface $db) {}

    public function __invoke(ServerRequest $request): Response
    {
        $routeParams = $request->getAttribute('routeParams', []);
        $id = $routeParams['id'] ?? null;

        if (!is_numeric($id)) {
            return ResponseFactory::badRequest(['error' => 'Invalid encounter ID']);
        }

        /** @var Result $result */
        $result = await($this->db->query($this->getQuery(), [$id]));
        $row = $result->rows[0] ?? null;

        if ($row === null) {
            return ResponseFactory::notFound(['error' => 'Encounter not found']);
        }

        $encounter = Encounter::fromDatabaseRow($row);

        return ResponseFactory::ok(['encounter' => $encounter]);
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
            WHERE e.id = ?
        SQL;
    }
}
