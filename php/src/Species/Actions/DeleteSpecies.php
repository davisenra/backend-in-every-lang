<?php

declare(strict_types=1);

namespace App\Species\Actions;

use App\Http\HttpAction;
use App\Http\ResponseFactory;
use Clue\React\SQLite\DatabaseInterface;
use React\Http\Message\Response;
use React\Http\Message\ServerRequest;

use function React\Async\await;

final readonly class DeleteSpecies implements HttpAction
{
    public function __construct(private DatabaseInterface $db) {}

    public function __invoke(ServerRequest $request): Response
    {
        $routeParams = $request->getAttribute('routeParams', []);
        $id = $routeParams['id'] ?? null;

        if (!is_numeric($id)) {
            return ResponseFactory::badRequest(['error' => 'Invalid species ID']);
        }

        await($this->db->query($this->getDeleteQuery(), [$id]));

        return ResponseFactory::noContent();
    }

    private function getDeleteQuery(): string
    {
        return <<<SQL
            DELETE FROM species
            WHERE id = ?
        SQL;
    }
}
