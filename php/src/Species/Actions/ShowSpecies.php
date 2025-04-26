<?php

declare(strict_types=1);

namespace App\Species\Actions;

use App\Http\HttpAction;
use App\Http\ResponseFactory;
use App\Species\Species;
use Clue\React\SQLite\DatabaseInterface;
use Clue\React\SQLite\Result;
use React\Http\Message\Response;
use React\Http\Message\ServerRequest;

use function React\Async\await;

final readonly class ShowSpecies implements HttpAction
{
    public function __construct(private DatabaseInterface $db) {}

    public function __invoke(ServerRequest $request): Response
    {
        $routeParams = $request->getAttribute('routeParams', []);
        $id = $routeParams['id'] ?? null;

        if (!is_numeric($id)) {
            return ResponseFactory::badRequest(['error' => 'Invalid species ID']);
        }

        /** @var Result $result */
        $result = await($this->db->query($this->getQuery(), [$id]));
        $row = $result->rows[0] ?? null;

        if ($row === null) {
            return ResponseFactory::notFound(['error' => 'Species not found']);
        }

        $species = Species::fromDatabaseRow($row);

        return ResponseFactory::ok(['species' => $species]);
    }

    private function getQuery(): string
    {
        return <<<SQL
            SELECT
                s.id as "id",
                s.name as "name"
            FROM species s
            WHERE s.id = ?
        SQL;
    }
}
