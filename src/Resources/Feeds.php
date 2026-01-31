<?php

declare(strict_types=1);

namespace VeilMail\Resources;

use VeilMail\HttpClient;

/**
 * RSS feed management.
 */
class Feeds
{
    public function __construct(private readonly HttpClient $http) {}

    /**
     * Create a new RSS feed.
     *
     * @param array{
     *     name: string,
     *     url: string,
     *     audienceId: string,
     *     pollInterval?: string,
     *     mode?: string,
     *     subjectTemplate?: string,
     *     htmlTemplate?: string,
     * } $params
     *
     * @return array<string, mixed>
     */
    public function create(array $params): array
    {
        return $this->http->post('/v1/feeds', $params);
    }

    /**
     * List all RSS feeds.
     *
     * @return array<string, mixed>
     */
    public function list(): array
    {
        return $this->http->get('/v1/feeds');
    }

    /**
     * Get a single feed by ID.
     *
     * @return array<string, mixed>
     */
    public function get(string $id): array
    {
        return $this->http->get("/v1/feeds/{$id}");
    }

    /**
     * Update a feed.
     *
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     */
    public function update(string $id, array $params): array
    {
        return $this->http->put("/v1/feeds/{$id}", $params);
    }

    /**
     * Delete a feed and all its items.
     */
    public function delete(string $id): void
    {
        $this->http->delete("/v1/feeds/{$id}");
    }

    /**
     * Manually trigger a feed poll.
     *
     * @return array<string, mixed>
     */
    public function poll(string $id): array
    {
        return $this->http->post("/v1/feeds/{$id}/poll");
    }

    /**
     * Pause an active feed.
     *
     * @return array<string, mixed>
     */
    public function pause(string $id): array
    {
        return $this->http->post("/v1/feeds/{$id}/pause");
    }

    /**
     * Resume a paused or errored feed.
     *
     * @return array<string, mixed>
     */
    public function resume(string $id): array
    {
        return $this->http->post("/v1/feeds/{$id}/resume");
    }

    /**
     * List feed items with pagination.
     *
     * @param array{limit?: int, cursor?: string, processed?: bool} $params
     *
     * @return array<string, mixed>
     */
    public function listItems(string $feedId, array $params = []): array
    {
        $query = [
            'limit' => $params['limit'] ?? null,
            'cursor' => $params['cursor'] ?? null,
        ];

        if (isset($params['processed'])) {
            $query['processed'] = $params['processed'] ? 'true' : 'false';
        }

        return $this->http->get("/v1/feeds/{$feedId}/items", $query);
    }
}
