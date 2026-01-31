<?php

declare(strict_types=1);

namespace VeilMail\Resources;

use VeilMail\HttpClient;

/**
 * Signup form management.
 */
class Forms
{
    public function __construct(private readonly HttpClient $http) {}

    /**
     * Create a new signup form.
     *
     * @param array{
     *     name: string,
     *     audienceId: string,
     *     fields?: array<int, array<string, mixed>>,
     *     doubleOptIn?: bool,
     *     redirectUrl?: string,
     *     honeypot?: bool,
     *     caslConsent?: bool,
     * } $params
     *
     * @return array<string, mixed>
     */
    public function create(array $params): array
    {
        return $this->http->post('/v1/forms', $params);
    }

    /**
     * List all signup forms.
     *
     * @param array{limit?: int, cursor?: string} $params
     *
     * @return array<string, mixed>
     */
    public function list(array $params = []): array
    {
        return $this->http->get('/v1/forms', [
            'limit' => $params['limit'] ?? null,
            'cursor' => $params['cursor'] ?? null,
        ]);
    }

    /**
     * Get a single form by ID.
     *
     * @return array<string, mixed>
     */
    public function get(string $id): array
    {
        return $this->http->get("/v1/forms/{$id}");
    }

    /**
     * Update a form.
     *
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     */
    public function update(string $id, array $params): array
    {
        return $this->http->put("/v1/forms/{$id}", $params);
    }

    /**
     * Delete a form.
     */
    public function delete(string $id): void
    {
        $this->http->delete("/v1/forms/{$id}");
    }
}
