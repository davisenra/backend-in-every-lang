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

        $this->assertEquals(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }
}
