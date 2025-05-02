<?php

declare(strict_types=1);

namespace App\Encounters\Actions;

use App\Http\HttpAction;
use App\Http\ResponseFactory;
use App\Encounters\Encounter;
use Clue\React\SQLite\DatabaseInterface;
use React\Http\Message\Response;
use React\Http\Message\ServerRequest;

use function React\Async\await;

final readonly class UpdateEncounter implements HttpAction
{
    public function __construct(private DatabaseInterface $db) {}

    public function __invoke(ServerRequest $request): Response
    {
        $routeParams = $request->getAttribute('routeParams', []);
        $id = $routeParams['id'] ?? null;

        if (!is_numeric($id)) {
            return ResponseFactory::badRequest(['error' => 'Invalid encounter ID']);
        }

        $body = json_decode($request->getBody()->getContents(), true);

        if (!is_array($body)) {
            return ResponseFactory::badRequest(['error' => 'Invalid request body']);
        }

        $location = $body['location'] ?? null;
        $description = $body['description'] ?? null;

        if (empty($location) || !is_string($location)) {
            return ResponseFactory::badRequest(['error' => 'Location is required and must be a string']);
        }

        if (!is_string($description)) {
            return ResponseFactory::badRequest(['error' => 'Description must be a string if provided']);
        }

        $existing = await($this->db->query(
            'SELECT * FROM encounters WHERE id = ? LIMIT 1',
            [$id],
        ));

        if (!$existing->rows) {
            return ResponseFactory::notFound(['error' => 'Encounter not found']);
        }

        await($this->db->query(
            $this->getQuery(),
            [$location, $description, $id],
        ));

        $encounter = Encounter::fromDatabaseRow([
            'id' => $id,
            'location' => $location,
            'description' => $description,
            'species_id' => $existing->rows[0]['id'],
        ]);

        return ResponseFactory::ok(['encounter' => $encounter]);
    }

    private function getQuery(): string
    {
        return <<<SQL
            UPDATE encounters 
            SET 
                location = ?,
                description = ?
            WHERE id = ?
        SQL;
    }
}
