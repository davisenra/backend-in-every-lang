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

final readonly class RegisterSpecies implements HttpAction
{
    public function __construct(private DatabaseInterface $db) {}

    public function __invoke(ServerRequest $request): Response
    {
        $body = json_decode((string) $request->getBody(), true);

        if (!is_array($body)) {
            return ResponseFactory::badRequest(['error' => 'Invalid request body']);
        }

        $name = $body['name'] ?? null;

        if (empty($name) || !is_string($name)) {
            return ResponseFactory::badRequest(['error' => 'Name is required and must be a string']);
        }

        /** @var Result $result */
        $result = await($this->db->query(
            $this->getQuery(),
            [$name],
        ));

        $speciesId = $result->insertId;

        if ($speciesId === 0) {
            return ResponseFactory::serverError(['error' => 'Error while registering species']);
        }

        $species = Species::fromDatabaseRow([
            'id' => $speciesId,
            'name' => $name,
        ]);

        return ResponseFactory::created(['species' => $species]);
    }

    private function getQuery(): string
    {
        return <<<SQL
            INSERT INTO species (name)
            VALUES (?)
        SQL;
    }
}
