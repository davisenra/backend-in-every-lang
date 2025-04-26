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

final readonly class RegisterEncounter implements HttpAction
{
    public function __construct(private DatabaseInterface $db) {}

    public function __invoke(ServerRequest $request): Response
    {
        $body = json_decode((string) $request->getBody(), true);

        if (!is_array($body)) {
            return ResponseFactory::badRequest(['error' => 'Invalid request body']);
        }

        $location = $body['location'] ?? null;
        $description = $body['description'] ?? null;
        $speciesId = $body['species_id'] ?? null;

        if (empty($location) || !is_string($location)) {
            return ResponseFactory::badRequest(['error' => 'Location is required and must be a string']);
        }

        if (!is_string($description)) {
            return ResponseFactory::badRequest(['error' => 'Description must be a string if provided']);
        }

        if (!is_numeric($speciesId)) {
            return ResponseFactory::badRequest(['error' => 'Species ID must be numeric']);
        }

        /** @var Result $result */
        $result = await($this->db->query(
            $this->getQuery(),
            [$location, $description, $speciesId],
        ));

        $encounterId = $result->insertId;

        if ($encounterId === 0) {
            return ResponseFactory::serverError(['error' => 'Error while registering encounter']);
        }

        $encounter = Encounter::fromDatabaseRow([
            'id' => $encounterId,
            'species_id' => $speciesId,
            'location' => $location,
            'description' => $description,
        ]);

        return ResponseFactory::created(['encounter' => $encounter]);
    }

    private function getQuery(): string
    {
        return <<<SQL
            INSERT INTO encounters (location, description, species_id)
            VALUES (?, ?, ?)
        SQL;
    }
}
