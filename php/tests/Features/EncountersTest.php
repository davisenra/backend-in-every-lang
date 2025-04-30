<?php

declare(strict_types=1);

namespace Features;

use Fig\Http\Message\StatusCodeInterface;
use Tests\Support\ApplicationTestCase;
use PHPUnit\Framework\Attributes\Test;

use function React\Async\await;

class EncountersTest extends ApplicationTestCase
{
    #[Test]
    public function listEncounters(): void
    {
        await($this->database->exec('INSERT INTO encounters (location, description, species_id) VALUES ("Foo", "Bar", "999")'));

        $response = $this->get('/encounters');

        $this->assertIsJson($response)
            ->assertStatusCode(StatusCodeInterface::STATUS_OK)
            ->assertIsObject()
            ->assertIsArray('encounters')
            ->assertIsNumeric('encounters.0.id')
            ->assertSame('Foo', 'encounters.0.location')
            ->assertSame('Bar', 'encounters.0.description');
    }

    #[Test]
    public function showExistingEncounter(): void
    {
        $result = await($this->database->query('INSERT INTO encounters (location, description, species_id) VALUES ("Foo", "Bar", "999")'));

        $existingId = $result->insertId;
        $response = $this->get("/encounters/$existingId");

        $this->assertIsJson($response)
            ->assertStatusCode(StatusCodeInterface::STATUS_OK)
            ->assertIsObject()
            ->assertIsObject('encounter')
            ->assertIsNumeric('encounter.id')
            ->assertSame($existingId, 'encounter.id')
            ->assertSame('Foo', 'encounter.location')
            ->assertSame('Bar', 'encounter.description');
    }

    #[Test]
    public function showNonExistentEncounter(): void
    {
        $response = $this->get("/encounters/99999999");

        $this->assertIsJson($response)
            ->assertStatusCode(StatusCodeInterface::STATUS_NOT_FOUND);
    }

    #[Test]
    public function createEncounter(): void
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
    public function deleteEncounter(): void
    {
        $result = await($this->database->query('INSERT INTO encounters (location, description, species_id) VALUES ("Foo", "Bar", "999")'));

        $existingId = $result->insertId;
        $response = $this->delete("/encounters/$existingId");
        $this->assertEquals(StatusCodeInterface::STATUS_NO_CONTENT, $response->getStatusCode());
        $this->assertEmpty((string) $response->getBody());
    }

    #[Test]
    public function deleteNonExistentEncounter(): void
    {
        $nonExistentId = 99999;
        $response = $this->delete("/encounters/$nonExistentId");

        // should still return a 204 if the entity does not exists
        $this->assertEquals(StatusCodeInterface::STATUS_NO_CONTENT, $response->getStatusCode());
        $this->assertEmpty((string) $response->getBody());
    }
}
