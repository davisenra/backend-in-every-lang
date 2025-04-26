<?php

declare(strict_types=1);

namespace Features;

use Fig\Http\Message\StatusCodeInterface;
use Tests\HttpTestCase;
use PHPUnit\Framework\Attributes\Test;

class SpeciesTest extends HttpTestCase
{
    #[Test]
    public function testListSpecies(): void
    {
        $response = $this->get('/species');

        $this->assertIsJson($response)
            ->assertStatusCode(StatusCodeInterface::STATUS_OK)
            ->assertIsObject()
            ->assertIsArray('species')
            ->assertIsNumeric('species.0.id');
    }

    #[Test]
    public function testShowExistingSpecies(): void
    {
        $existingId = 5173;
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
