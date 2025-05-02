<?php

declare(strict_types=1);

namespace App\Species\Actions;

use App\Http\HttpAction;
use App\Http\ResponseFactory;
use App\Species\Species;
use Clue\React\SQLite\DatabaseInterface;
use React\Http\Message\Response;
use React\Http\Message\ServerRequest;

use function React\Async\await;

final readonly class UpdateSpecies implements HttpAction
{
    public function __construct(private DatabaseInterface $db) {}

    public function __invoke(ServerRequest $request): Response
    {
        $routeParams = $request->getAttribute('routeParams', []);
        $id = $routeParams['id'] ?? null;

        if (!is_numeric($id)) {
            return ResponseFactory::badRequest(['error' => 'Invalid species ID']);
        }

        $body = json_decode($request->getBody()->getContents(), true);

        if (!is_array($body)) {
            return ResponseFactory::badRequest(['error' => 'Invalid request body']);
        }

        $name = $body['name'] ?? null;

        if (empty($name) || !is_string($name)) {
            return ResponseFactory::badRequest(['error' => 'Name is required and must be a string']);
        }

        $existing = await($this->db->query(
            'SELECT * FROM species WHERE id = ? LIMIT 1',
            [$id],
        ));

        if (!$existing->rows) {
            return ResponseFactory::notFound(['error' => 'Species not found']);
        }

        await($this->db->query(
            $this->getQuery(),
            [$name],
        ));

        $species = Species::fromDatabaseRow([
            'id' => $id,
            'name' => $name,
        ]);

        return ResponseFactory::ok(['species' => $species]);
    }

    private function getQuery(): string
    {
        return <<<SQL
            UPDATE species 
            SET 
                name = ?
            WHERE id = ?
        SQL;
    }
}
