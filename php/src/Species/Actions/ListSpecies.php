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

final readonly class ListSpecies implements HttpAction
{
    public function __construct(private DatabaseInterface $db) {}

    public function __invoke(ServerRequest $request): Response
    {
        /** @var Result $result */
        $result = await($this->db->query($this->getQuery()));
        $species = array_map(fn($r) => Species::fromDatabaseRow($r), $result->rows);

        return ResponseFactory::ok(['species' => $species]);
    }

    private function getQuery(): string
    {
        return <<<SQL
            SELECT
                s.id as "id",
                s.name as "name"
            FROM species s
            ORDER BY s.id DESC
        SQL;
    }
}
