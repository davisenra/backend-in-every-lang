<?php

declare(strict_types=1);

namespace Features;

use Fig\Http\Message\StatusCodeInterface;
use Tests\Support\ApplicationTestCase;
use PHPUnit\Framework\Attributes\Test;

use function React\Async\await;

class SpeciesTest extends ApplicationTestCase
{
    #[Test]
    public function listSpecies(): void
    {
        await($this->database->exec('INSERT INTO species (name) VALUES ("Foo")'));

        $response = $this->get('/species');

        $this->assertIsJson($response)
            ->assertStatusCode(StatusCodeInterface::STATUS_OK)
            ->assertIsObject()
            ->assertIsArray('species')
            ->assertIsNumeric('species.0.id')
            ->assertSame('Foo', 'species.0.name');
    }

    #[Test]
    public function showExistingSpecies(): void
    {
        $result = await($this->database->query('INSERT INTO species (name) VALUES ("Foo")'));

        $existingId = $result->insertId;
        $response = $this->get("/species/$existingId");

        $this->assertIsJson($response)
            ->assertStatusCode(StatusCodeInterface::STATUS_OK)
            ->assertIsObject()
            ->assertIsObject('species')
            ->assertIsNumeric('species.id')
            ->assertSame($existingId, 'species.id');
    }

    #[Test]
    public function showNonExistentSpecies(): void
    {
        $response = $this->get("/species/99999999");

        $this->assertIsJson($response)
            ->assertStatusCode(StatusCodeInterface::STATUS_NOT_FOUND);
    }

    #[Test]
    public function updateExistingSpecies(): void
    {
        $result = await($this->database->query(
            'INSERT INTO species (name) VALUES ("Foo")',
        ));

        $existingId = $result->insertId;

        $payload = [
            'name' => 'Bar',
        ];

        $response = $this->patch("/species/$existingId", $payload);

        $this->assertIsJson($response)
            ->assertStatusCode(StatusCodeInterface::STATUS_OK)
            ->assertIsObject()
            ->assertIsObject('species')
            ->assertSame($existingId, 'species.id')
            ->assertSame('Bar', 'species.name');
    }

    #[Test]
    public function updateNonExistentSpecies(): void
    {
        $nonExistentId = 99999;
        $payload = [
            'name' => 'Foo',
        ];

        $response = $this->patch("/species/$nonExistentId", $payload);

        $this->assertIsJson($response)
            ->assertStatusCode(StatusCodeInterface::STATUS_NOT_FOUND);
    }

    #[Test]
    public function deleteSpecies(): void
    {
        $result = await($this->database->query(
            'INSERT INTO species (name) VALUES ("Foo")',
        ));

        $existingId = $result->insertId;
        $response = $this->delete("/species/$existingId");
        $this->assertEquals(StatusCodeInterface::STATUS_NO_CONTENT, $response->getStatusCode());
        $this->assertEmpty((string) $response->getBody());
    }

    #[Test]
    public function deleteNonExistentSpecies(): void
    {
        $nonExistentId = 99999;
        $response = $this->delete("/species/$nonExistentId");

        // should still return a 204 if the entity does not exists
        $this->assertEquals(StatusCodeInterface::STATUS_NO_CONTENT, $response->getStatusCode());
        $this->assertEmpty((string) $response->getBody());
    }

    #[Test]
    public function createSpecies(): void
    {
        $payload = [
            'name' => 'Foo',
        ];

        $response = $this->post('/species', $payload);

        $this->assertIsJson($response)
            ->assertStatusCode(StatusCodeInterface::STATUS_CREATED)
            ->assertIsObject()
            ->assertIsObject('species')
            ->assertIsNumeric('species.id')
            ->assertSame('Foo', 'species.name');
    }
}
