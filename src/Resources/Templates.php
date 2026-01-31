<?php

declare(strict_types=1);

namespace VeilMail\Resources;

use VeilMail\HttpClient;

/**
 * Email template management.
 */
class Templates
{
    public function __construct(private readonly HttpClient $http) {}

    /**
     * Create a new template.
     *
     * @param array{
     *     name: string,
     *     subject: string,
     *     html: string,
     *     text?: string,
     *     description?: string,
     *     variables?: array<array{name: string, type?: string, required?: bool, defaultValue?: string, description?: string}>,
     * } $params
     *
     * @return array<string, mixed> Created template
     */
    public function create(array $params): array
    {
        $response = $this->http->post('/v1/templates', $params);

        return $response['data'] ?? $response;
    }

    /**
     * List all templates.
     *
     * @param array{limit?: int, cursor?: string} $params
     *
     * @return array<string, mixed> Paginated list
     */
    public function list(array $params = []): array
    {
        return $this->http->get('/v1/templates', [
            'limit' => $params['limit'] ?? null,
            'cursor' => $params['cursor'] ?? null,
        ]);
    }

    /**
     * Get a single template by ID.
     *
     * @return array<string, mixed> Template details
     */
    public function get(string $id): array
    {
        $response = $this->http->get("/v1/templates/{$id}");

        return $response['data'] ?? $response;
    }

    /**
     * Update a template.
     *
     * @param array{
     *     name?: string,
     *     subject?: string,
     *     html?: string,
     *     text?: string,
     *     description?: string,
     *     variables?: array<array{name: string, type?: string, required?: bool, defaultValue?: string, description?: string}>,
     * } $params
     *
     * @return array<string, mixed> Updated template
     */
    public function update(string $id, array $params): array
    {
        $response = $this->http->patch("/v1/templates/{$id}", $params);

        return $response['data'] ?? $response;
    }

    /**
     * Preview a template with variables.
     *
     * @param array{html: string, variables?: array<string, mixed>} $params
     *
     * @return array<string, mixed> Preview result with rendered HTML
     */
    public function preview(array $params): array
    {
        return $this->http->post('/v1/templates/preview', $params);
    }

    /**
     * Delete a template.
     */
    public function delete(string $id): void
    {
        $this->http->delete("/v1/templates/{$id}");
    }
}
