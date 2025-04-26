<?php

declare(strict_types=1);

namespace App\Http;

use Fig\Http\Message\StatusCodeInterface;
use React\Http\Message\Response;

final class JsonResponse
{
    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $headers
     * @throws \JsonException
     */
    public static function build(array $data, int $status = StatusCodeInterface::STATUS_OK, array $headers = []): Response
    {
        $defaultHeaders = ['Content-Type' => 'application/json'];
        $mergedHeaders = array_merge($defaultHeaders, $headers);
        $jsonEncodedData = json_encode($data, JSON_THROW_ON_ERROR);

        return new Response($status, $mergedHeaders, $jsonEncodedData);
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $headers
     * @throws \JsonException
     */
    public static function ok(array $data, array $headers = []): Response
    {
        return self::build($data, StatusCodeInterface::STATUS_OK, $headers);
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $headers
     * @throws \JsonException
     */
    public static function notFound(array $data, array $headers = []): Response
    {
        return self::build($data, StatusCodeInterface::STATUS_NOT_FOUND, $headers);
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $headers
     * @throws \JsonException
     */
    public static function badRequest(array $data, array $headers = []): Response
    {
        return self::build($data, StatusCodeInterface::STATUS_BAD_REQUEST, $headers);
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $headers
     * @throws \JsonException
     */
    public static function serverError(array $data, array $headers = []): Response
    {
        return self::build($data, StatusCodeInterface::STATUS_BAD_REQUEST, $headers);
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $headers
     * @throws \JsonException
     */
    public static function created(array $data, array $headers = []): Response
    {
        return self::build($data, StatusCodeInterface::STATUS_CREATED, $headers);
    }
}
