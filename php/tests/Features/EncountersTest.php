<?php

declare(strict_types=1);

namespace Features;

use Fig\Http\Message\StatusCodeInterface;
use Tests\HttpTestCase;
use PHPUnit\Framework\Attributes\Test;

class EncountersTest extends HttpTestCase
{
    #[Test]
    public function testListEncounters(): void
    {
        $response = $this->get('/encounters');

        $this->assertIsJson($response)
            ->assertStatusCode(StatusCodeInterface::STATUS_OK)
            ->assertIsObject()
            ->assertIsArray('encounters')
            ->assertIsNumeric('encounters.0.id');
    }

    #[Test]
    public function testShowExistingEncounter(): void
    {
        $existingId = 1337;
        $response = $this->get("/encounters/$existingId");

        $this->assertIsJson($response)
            ->assertStatusCode(StatusCodeInterface::STATUS_OK)
            ->assertIsObject()
            ->assertIsObject('encounter')
            ->assertIsNumeric('encounter.id')
            ->assertSame($existingId, 'encounter.id');
    }

    #[Test]
    public function testShowNonExistentEncounter(): void
    {
        $response = $this->get("/encounters/99999999");

        $this->assertIsJson($response)
            ->assertStatusCode(StatusCodeInterface::STATUS_NOT_FOUND);
    }

    #[Test]
    public function testCreateEncounter(): void
    {
        $payload = [
            'location' => 'Redwood Forest',
            'description' => 'Spotted near a creek at sunset',
            'species_id' => 5173,
        ];

        $response = $this->post('/encounters', $payload);

        $this->assertIsJson($response)
            ->assertStatusCode(StatusCodeInterface::STATUS_CREATED)
            ->assertIsObject()
            ->assertIsObject('encounter')
            ->assertIsNumeric('encounter.id')
            ->assertSame('Redwood Forest', 'encounter.location')
            ->assertSame('Spotted near a creek at sunset', 'encounter.description')
            ->assertSame(5173, 'encounter.speciesId');
    }

    #[Test]
    public function testDeleteEncounter(): void
    {
        $existingId = 1336;
        $response = $this->delete("/encounters/$existingId");
        $this->assertEquals(StatusCodeInterface::STATUS_NO_CONTENT, $response->getStatusCode());
        $this->assertEmpty((string) $response->getBody());
    }

    #[Test]
    public function testDeleteNonExistentEncounter(): void
    {
        $nonExistentId = 99999;
        $response = $this->delete("/encounters/$nonExistentId");

        // should still return a 204 if the entity does not exists
        $this->assertEquals(StatusCodeInterface::STATUS_NO_CONTENT, $response->getStatusCode());
        $this->assertEmpty((string) $response->getBody());
    }
}
