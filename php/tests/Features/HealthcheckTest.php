<?php

declare(strict_types=1);

namespace Features;

use Fig\Http\Message\StatusCodeInterface;
use Tests\Support\ApplicationTestCase;
use PHPUnit\Framework\Attributes\Test;

class HealthcheckTest extends ApplicationTestCase
{
    #[Test]
    public function healthcheck(): void
    {
        $response = $this->get('/healthcheck');

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }
}
