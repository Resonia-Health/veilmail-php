<?php

declare(strict_types=1);

namespace VeilMail\Resources;

use VeilMail\HttpClient;

/**
 * Subscriber management within an audience.
 */
class Subscribers
{
    private string $basePath;

    public function __construct(
        private readonly HttpClient $http,
        string $audienceId,
    ) {
        $this->basePath = "/v1/audiences/{$audienceId}/subscribers";
    }

    /**
     * List subscribers with optional filters.
     *
     * @param array{limit?: int, cursor?: string, status?: string, email?: string} $params
     *
     * @return array<string, mixed> Paginated list
     */
    public function list(array $params = []): array
    {
        return $this->http->get($this->basePath, [
            'limit' => $params['limit'] ?? null,
            'cursor' => $params['cursor'] ?? null,
            'status' => $params['status'] ?? null,
            'email' => $params['email'] ?? null,
        ]);
    }

    /**
     * Add a subscriber.
     *
     * @param array{
     *     email: string,
     *     firstName?: string,
     *     lastName?: string,
     *     metadata?: array<string, mixed>,
     *     doubleOptIn?: bool,
     *     status?: string,
     *     consentType?: string,
     *     consentSource?: string,
     *     consentDate?: string,
     *     consentExpiresAt?: string,
     *     consentProof?: string,
     * } $params
     *
     * @return array<string, mixed> Created subscriber
     */
    public function add(array $params): array
    {
        $response = $this->http->post($this->basePath, $params);

        return $response['data'] ?? $response;
    }

    /**
     * Get a single subscriber by ID.
     *
     * @return array<string, mixed> Subscriber details
     */
    public function get(string $subscriberId): array
    {
        $response = $this->http->get("{$this->basePath}/{$subscriberId}");

        return $response['data'] ?? $response;
    }

    /**
     * Update a subscriber.
     *
     * @param array{firstName?: string, lastName?: string, metadata?: array<string, mixed>, status?: string} $params
     *
     * @return array<string, mixed> Updated subscriber
     */
    public function update(string $subscriberId, array $params): array
    {
        $response = $this->http->put("{$this->basePath}/{$subscriberId}", $params);

        return $response['data'] ?? $response;
    }

    /**
     * Remove a subscriber.
     */
    public function remove(string $subscriberId): void
    {
        $this->http->delete("{$this->basePath}/{$subscriberId}");
    }

    /**
     * Confirm a double opt-in subscriber.
     *
     * @return array<string, mixed> Confirmed subscriber
     */
    public function confirm(string $subscriberId): array
    {
        $response = $this->http->post("{$this->basePath}/{$subscriberId}/confirm");

        return $response['data'] ?? $response;
    }

    /**
     * Bulk import subscribers.
     *
     * @param array{subscribers?: array<array<string, mixed>>, csvData?: string} $params
     *
     * @return array<string, mixed> Import results with created/skipped counts
     */
    public function import(array $params): array
    {
        return $this->http->post("{$this->basePath}/import", $params);
    }

    /**
     * Export subscribers as CSV.
     *
     * @param array{status?: string} $params
     *
     * @return string CSV data
     */
    public function export(array $params = []): string
    {
        return $this->http->getRaw("{$this->basePath}/export", [
            'status' => $params['status'] ?? null,
        ]);
    }

    /**
     * Get a subscriber's activity timeline.
     *
     * @param array{limit?: int, cursor?: string, type?: string} $params
     *
     * @return array<string, mixed> Paginated activity events
     */
    public function activity(string $subscriberId, array $params = []): array
    {
        return $this->http->get("{$this->basePath}/{$subscriberId}/activity", [
            'limit' => $params['limit'] ?? null,
            'cursor' => $params['cursor'] ?? null,
            'type' => $params['type'] ?? null,
        ]);
    }
}
