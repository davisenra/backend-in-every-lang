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
        // assumes you ran the migration
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
}
