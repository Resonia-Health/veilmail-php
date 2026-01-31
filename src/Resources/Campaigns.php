<?php

declare(strict_types=1);

namespace VeilMail\Resources;

use VeilMail\HttpClient;

/**
 * Campaign management.
 */
class Campaigns
{
    public function __construct(private readonly HttpClient $http) {}

    /**
     * Create a new campaign.
     *
     * @param array{
     *     name: string,
     *     subject: string,
     *     from: string,
     *     audienceId: string,
     *     html?: string,
     *     text?: string,
     *     templateId?: string,
     *     replyTo?: string,
     *     previewText?: string,
     *     tags?: string[],
     * } $params
     *
     * @return array<string, mixed> Created campaign
     */
    public function create(array $params): array
    {
        $response = $this->http->post('/v1/campaigns', $params);

        return $response['data'] ?? $response;
    }

    /**
     * List all campaigns.
     *
     * @param array{limit?: int, cursor?: string, status?: string} $params
     *
     * @return array<string, mixed> Paginated list
     */
    public function list(array $params = []): array
    {
        return $this->http->get('/v1/campaigns', [
            'limit' => $params['limit'] ?? null,
            'cursor' => $params['cursor'] ?? null,
            'status' => $params['status'] ?? null,
        ]);
    }

    /**
     * Get a single campaign by ID.
     *
     * @return array<string, mixed> Campaign details with stats
     */
    public function get(string $id): array
    {
        $response = $this->http->get("/v1/campaigns/{$id}");

        return $response['data'] ?? $response;
    }

    /**
     * Update a campaign.
     *
     * @param array{
     *     name?: string,
     *     subject?: string,
     *     from?: string,
     *     html?: string,
     *     text?: string,
     *     templateId?: string,
     *     replyTo?: string,
     *     previewText?: string,
     *     tags?: string[],
     * } $params
     *
     * @return array<string, mixed> Updated campaign
     */
    public function update(string $id, array $params): array
    {
        $response = $this->http->patch("/v1/campaigns/{$id}", $params);

        return $response['data'] ?? $response;
    }

    /**
     * Delete a campaign (draft only).
     */
    public function delete(string $id): void
    {
        $this->http->delete("/v1/campaigns/{$id}");
    }

    /**
     * Schedule a campaign for future delivery.
     *
     * @param array{scheduledAt: string} $params
     *
     * @return array<string, mixed> Scheduled campaign
     */
    public function schedule(string $id, array $params): array
    {
        $response = $this->http->post("/v1/campaigns/{$id}/schedule", $params);

        return $response['data'] ?? $response;
    }

    /**
     * Send a campaign immediately.
     *
     * @return array<string, mixed> Campaign send result
     */
    public function send(string $id): array
    {
        $response = $this->http->post("/v1/campaigns/{$id}/send");

        return $response['data'] ?? $response;
    }

    /**
     * Pause a sending campaign.
     *
     * @return array<string, mixed> Paused campaign
     */
    public function pause(string $id): array
    {
        $response = $this->http->post("/v1/campaigns/{$id}/pause");

        return $response['data'] ?? $response;
    }

    /**
     * Resume a paused campaign.
     *
     * @return array<string, mixed> Resumed campaign
     */
    public function resume(string $id): array
    {
        $response = $this->http->post("/v1/campaigns/{$id}/resume");

        return $response['data'] ?? $response;
    }

    /**
     * Cancel a campaign.
     *
     * @return array<string, mixed> Cancelled campaign
     */
    public function cancel(string $id): array
    {
        $response = $this->http->post("/v1/campaigns/{$id}/cancel");

        return $response['data'] ?? $response;
    }

    /**
     * Send a test/preview of a campaign.
     *
     * @param string[] $to Test email addresses (max 5)
     *
     * @return array<string, mixed> Test send result
     */
    public function sendTest(string $id, array $to): array
    {
        return $this->http->post("/v1/campaigns/{$id}/test", ['to' => $to]);
    }

    /**
     * Clone a campaign as a new draft.
     *
     * @param array{includeABTest?: bool} $options
     *
     * @return array<string, mixed> Cloned campaign
     */
    public function clone(string $id, array $options = []): array
    {
        return $this->http->post("/v1/campaigns/{$id}/clone", $options);
    }

    /**
     * Get tracked link analytics for a campaign.
     *
     * @param array{limit?: int, sort?: string, order?: string} $params
     *
     * @return array<string, mixed> Link analytics data
     */
    public function links(string $id, array $params = []): array
    {
        return $this->http->get("/v1/campaigns/{$id}/links", [
            'limit' => $params['limit'] ?? null,
            'sort' => $params['sort'] ?? null,
            'order' => $params['order'] ?? null,
        ]);
    }
}
