<?php

declare(strict_types=1);

namespace VeilMail\Resources;

use VeilMail\HttpClient;

/**
 * Subscription topic management.
 */
class Topics
{
    public function __construct(private readonly HttpClient $http) {}

    /**
     * Create a subscription topic.
     *
     * @param array{
     *     name: string,
     *     description?: string,
     *     isDefault?: bool,
     *     sortOrder?: int,
     * } $params
     *
     * @return array<string, mixed> Created topic
     */
    public function create(array $params): array
    {
        return $this->http->post('/v1/topics', $params);
    }

    /**
     * List all subscription topics.
     *
     * @param array{active?: bool} $params
     *
     * @return array<string, mixed> List with data key
     */
    public function list(array $params = []): array
    {
        return $this->http->get('/v1/topics', [
            'active' => $params['active'] ?? null,
        ]);
    }

    /**
     * Get a single topic by ID.
     *
     * @return array<string, mixed> Topic details
     */
    public function get(string $id): array
    {
        return $this->http->get("/v1/topics/{$id}");
    }

    /**
     * Update a subscription topic.
     *
     * @param array{
     *     name?: string,
     *     description?: string,
     *     isDefault?: bool,
     *     sortOrder?: int,
     *     active?: bool,
     * } $params
     *
     * @return array<string, mixed> Updated topic
     */
    public function update(string $id, array $params): array
    {
        return $this->http->patch("/v1/topics/{$id}", $params);
    }

    /**
     * Deactivate a subscription topic (soft delete).
     */
    public function delete(string $id): void
    {
        $this->http->delete("/v1/topics/{$id}");
    }

    /**
     * Get a subscriber's topic preferences.
     *
     * @return array<string, mixed> Preferences with data key
     */
    public function getPreferences(string $audienceId, string $subscriberId): array
    {
        return $this->http->get("/v1/audiences/{$audienceId}/subscribers/{$subscriberId}/topics");
    }

    /**
     * Set a subscriber's topic preferences.
     *
     * @param array{topics: array<array{topicId: string, subscribed: bool}>} $params
     *
     * @return array<string, mixed> Updated preferences
     */
    public function setPreferences(string $audienceId, string $subscriberId, array $params): array
    {
        return $this->http->put(
            "/v1/audiences/{$audienceId}/subscribers/{$subscriberId}/topics",
            $params,
        );
    }
}
