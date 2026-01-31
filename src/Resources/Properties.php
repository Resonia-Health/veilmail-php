<?php

declare(strict_types=1);

namespace VeilMail\Resources;

use VeilMail\HttpClient;

/**
 * Contact property management.
 */
class Properties
{
    public function __construct(private readonly HttpClient $http) {}

    /**
     * Create a contact property.
     *
     * @param array{
     *     key: string,
     *     name: string,
     *     type?: string,
     *     description?: string,
     *     required?: bool,
     *     enumOptions?: string[],
     *     sortOrder?: int,
     * } $params
     *
     * @return array<string, mixed> Created property
     */
    public function create(array $params): array
    {
        $response = $this->http->post('/v1/properties', $params);

        return $response['data'] ?? $response;
    }

    /**
     * List all contact properties.
     *
     * @param array{active?: bool} $params
     *
     * @return array<string, mixed> List with data key
     */
    public function list(array $params = []): array
    {
        return $this->http->get('/v1/properties', [
            'active' => $params['active'] ?? null,
        ]);
    }

    /**
     * Get a single contact property by ID.
     *
     * @return array<string, mixed> Property details
     */
    public function get(string $id): array
    {
        $response = $this->http->get("/v1/properties/{$id}");

        return $response['data'] ?? $response;
    }

    /**
     * Update a contact property.
     *
     * @param array{
     *     name?: string,
     *     description?: string,
     *     required?: bool,
     *     enumOptions?: string[],
     *     sortOrder?: int,
     *     active?: bool,
     * } $params
     *
     * @return array<string, mixed> Updated property
     */
    public function update(string $id, array $params): array
    {
        $response = $this->http->patch("/v1/properties/{$id}", $params);

        return $response['data'] ?? $response;
    }

    /**
     * Deactivate a contact property (soft delete).
     */
    public function delete(string $id): void
    {
        $this->http->delete("/v1/properties/{$id}");
    }

    /**
     * Get a subscriber's property values.
     *
     * @return array<string, mixed> Property values
     */
    public function getValues(string $audienceId, string $subscriberId): array
    {
        return $this->http->get("/v1/audiences/{$audienceId}/subscribers/{$subscriberId}/properties");
    }

    /**
     * Set a subscriber's property values (merge with existing).
     *
     * Pass null for a value to delete it.
     *
     * @param array<string, string|int|float|bool|null> $values
     *
     * @return array<string, mixed> Result
     */
    public function setValues(string $audienceId, string $subscriberId, array $values): array
    {
        return $this->http->put(
            "/v1/audiences/{$audienceId}/subscribers/{$subscriberId}/properties",
            $values,
        );
    }
}
