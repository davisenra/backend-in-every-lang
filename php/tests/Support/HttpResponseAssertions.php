<?php

declare(strict_types=1);

namespace Tests\Support;

use PHPUnit\Framework\Assert;
use React\Http\Message\Response;

final class HttpResponseAssertions
{
    private mixed $jsonData;

    public function __construct(private readonly Response $response)
    {
        Assert::assertSame('application/json', $response->getHeaderLine('Content-Type'), 'Response is not JSON');
        $body = (string) $response->getBody();
        $this->jsonData = json_decode($body, false, 512, JSON_THROW_ON_ERROR);
    }

    public function assertStatusCode(int $statusCode): self
    {
        Assert::assertEquals($statusCode, $this->response->getStatusCode());
        return $this;
    }

    public function assertIsObject(string $path = ''): self
    {
        $data = $this->resolvePath($path);
        Assert::assertIsObject($data, "Expected object at path '{$path}'");
        return $this;
    }

    public function assertIsArray(string $path = ''): self
    {
        $data = $this->resolvePath($path);
        Assert::assertIsArray($data, "Expected array at path '{$path}'");
        return $this;
    }

    public function assertIsString(string $path = ''): self
    {
        $data = $this->resolvePath($path);
        Assert::assertIsString($data, "Expected string at path '{$path}'");
        return $this;
    }

    public function assertIsNumeric(string $path = ''): self
    {
        $data = $this->resolvePath($path);
        Assert::assertIsNumeric($data, "Expected numeric at path '{$path}'");
        return $this;
    }

    public function assertSame(mixed $expected, string $path = ''): self
    {
        $data = $this->resolvePath($path);
        Assert::assertSame($expected, $data, "Expected value '{$expected}' at path '{$path}', but got '" . print_r($data, true) . "'");
        return $this;
    }

    public function assertCount(int $expectedCount, string $path = ''): self
    {
        $data = $this->resolvePath($path);
        Assert::assertCount($expectedCount, $data, "Expected array at path '{$path}' to have {$expectedCount} elements");
        return $this;
    }

    private function resolvePath(string $path): mixed
    {
        if ($path === '') {
            return $this->jsonData;
        }

        $parts = explode('.', $path);
        $current = $this->jsonData;

        foreach ($parts as $part) {
            if (is_object($current) && property_exists($current, $part)) {
                $current = $current->$part;
            } elseif (is_array($current) && isset($current[$part])) {
                $current = $current[$part];
            } elseif (is_array($current) && is_numeric($part)) {
                $current = $current[(int) $part];
            } else {
                Assert::fail("Path '{$path}' does not exist in the JSON response");
            }
        }

        return $current;
    }
}
