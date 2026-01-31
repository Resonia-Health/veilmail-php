<?php

declare(strict_types=1);

namespace VeilMail\Resources;

use VeilMail\HttpClient;

/**
 * Domain management for email sending.
 */
class Domains
{
    public function __construct(private readonly HttpClient $http) {}

    /**
     * Add a new domain for verification.
     *
     * @param array{domain: string} $params
     *
     * @return array<string, mixed> Created domain with DNS records
     */
    public function create(array $params): array
    {
        $response = $this->http->post('/v1/domains', $params);

        return $response['data'] ?? $response;
    }

    /**
     * List all domains.
     *
     * @param array{limit?: int, cursor?: string} $params
     *
     * @return array<string, mixed> Paginated list
     */
    public function list(array $params = []): array
    {
        return $this->http->get('/v1/domains', [
            'limit' => $params['limit'] ?? null,
            'cursor' => $params['cursor'] ?? null,
        ]);
    }

    /**
     * Get a single domain by ID.
     *
     * @return array<string, mixed> Domain details
     */
    public function get(string $id): array
    {
        $response = $this->http->get("/v1/domains/{$id}");

        return $response['data'] ?? $response;
    }

    /**
     * Update domain settings (tracking, etc.).
     *
     * @param array{trackOpens?: bool, trackClicks?: bool} $params
     *
     * @return array<string, mixed> Updated domain
     */
    public function update(string $id, array $params): array
    {
        return $this->http->patch("/v1/domains/{$id}", $params);
    }

    /**
     * Trigger domain verification.
     *
     * @return array<string, mixed> Domain with updated verification status
     */
    public function verify(string $id): array
    {
        $response = $this->http->post("/v1/domains/{$id}/verify");

        return $response['data'] ?? $response;
    }

    /**
     * Delete a domain.
     */
    public function delete(string $id): void
    {
        $this->http->delete("/v1/domains/{$id}");
    }
}
