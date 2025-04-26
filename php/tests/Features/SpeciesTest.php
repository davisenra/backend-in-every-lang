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

        $this->assertEquals(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }
}
