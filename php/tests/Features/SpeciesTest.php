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
    public function testListSpecies(): void
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
    public function testShowExistingSpecies(): void
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
    public function testShowNonExistentSpecies(): void
    {
        $response = $this->get("/species/99999999");

        $this->assertIsJson($response)
            ->assertStatusCode(StatusCodeInterface::STATUS_NOT_FOUND);
    }
}
