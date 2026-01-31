<?php

declare(strict_types=1);

namespace VeilMail\Resources;

use VeilMail\HttpClient;

/**
 * Webhook endpoint management.
 */
class Webhooks
{
    public function __construct(private readonly HttpClient $http) {}

    /**
     * Create a webhook endpoint.
     *
     * @param array{
     *     url: string,
     *     events: string[],
     *     description?: string,
     *     enabled?: bool,
     * } $params
     *
     * @return array<string, mixed> Created webhook with signing secret
     */
    public function create(array $params): array
    {
        $response = $this->http->post('/v1/webhooks', $params);

        return $response['data'] ?? $response;
    }

    /**
     * List all webhook endpoints.
     *
     * @param array{limit?: int, cursor?: string} $params
     *
     * @return array<string, mixed> Paginated list
     */
    public function list(array $params = []): array
    {
        return $this->http->get('/v1/webhooks', [
            'limit' => $params['limit'] ?? null,
            'cursor' => $params['cursor'] ?? null,
        ]);
    }

    /**
     * Get a single webhook by ID.
     *
     * @return array<string, mixed> Webhook details
     */
    public function get(string $id): array
    {
        $response = $this->http->get("/v1/webhooks/{$id}");

        return $response['data'] ?? $response;
    }

    /**
     * Update a webhook endpoint.
     *
     * @param array{
     *     url?: string,
     *     events?: string[],
     *     description?: string,
     *     enabled?: bool,
     * } $params
     *
     * @return array<string, mixed> Updated webhook
     */
    public function update(string $id, array $params): array
    {
        $response = $this->http->patch("/v1/webhooks/{$id}", $params);

        return $response['data'] ?? $response;
    }

    /**
     * Delete a webhook endpoint.
     */
    public function delete(string $id): void
    {
        $this->http->delete("/v1/webhooks/{$id}");
    }

    /**
     * Send a test event to a webhook endpoint.
     *
     * @return array<string, mixed> Test result with response status
     */
    public function test(string $id): array
    {
        return $this->http->post("/v1/webhooks/{$id}/test");
    }

    /**
     * Rotate the signing secret for a webhook.
     *
     * @return array<string, mixed> Webhook with new secret
     */
    public function rotateSecret(string $id): array
    {
        $response = $this->http->post("/v1/webhooks/{$id}/rotate-secret");

        return $response['data'] ?? $response;
    }
}
