<?php

declare(strict_types=1);

namespace VeilMail\Resources;

use VeilMail\HttpClient;

/**
 * Audience management.
 */
class Audiences
{
    public function __construct(private readonly HttpClient $http) {}

    /**
     * Create a new audience.
     *
     * @param array{name: string, description?: string} $params
     *
     * @return array<string, mixed> Created audience
     */
    public function create(array $params): array
    {
        $response = $this->http->post('/v1/audiences', $params);

        return $response['data'] ?? $response;
    }

    /**
     * List all audiences.
     *
     * @param array{limit?: int, cursor?: string} $params
     *
     * @return array<string, mixed> Paginated list
     */
    public function list(array $params = []): array
    {
        return $this->http->get('/v1/audiences', [
            'limit' => $params['limit'] ?? null,
            'cursor' => $params['cursor'] ?? null,
        ]);
    }

    /**
     * Get a single audience by ID.
     *
     * @return array<string, mixed> Audience details
     */
    public function get(string $id): array
    {
        $response = $this->http->get("/v1/audiences/{$id}");

        return $response['data'] ?? $response;
    }

    /**
     * Update an audience.
     *
     * @param array{name?: string, description?: string} $params
     *
     * @return array<string, mixed> Updated audience
     */
    public function update(string $id, array $params): array
    {
        $response = $this->http->put("/v1/audiences/{$id}", $params);

        return $response['data'] ?? $response;
    }

    /**
     * Delete an audience.
     */
    public function delete(string $id): void
    {
        $this->http->delete("/v1/audiences/{$id}");
    }

    /**
     * Get a Subscribers helper scoped to the given audience.
     */
    public function subscribers(string $audienceId): Subscribers
    {
        return new Subscribers($this->http, $audienceId);
    }

    /**
     * Recalculate engagement scores for all subscribers.
     *
     * @return array<string, mixed> Result with processed count
     */
    public function recalculateEngagement(string $audienceId): array
    {
        return $this->http->post("/v1/audiences/{$audienceId}/recalculate-engagement", []);
    }

    /**
     * Get engagement statistics for an audience.
     *
     * @return array<string, mixed> Engagement stats
     */
    public function getEngagementStats(string $audienceId): array
    {
        return $this->http->get("/v1/audiences/{$audienceId}/engagement-stats");
    }
}
