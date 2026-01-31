<?php

declare(strict_types=1);

namespace VeilMail\Resources;

use VeilMail\HttpClient;

/**
 * Geo and device analytics.
 */
class Analytics
{
    public function __construct(private readonly HttpClient $http) {}

    /**
     * Get organization-level geo analytics.
     *
     * @param array{days?: int, eventType?: string} $params
     *
     * @return array<string, mixed>
     */
    public function geo(array $params = []): array
    {
        return $this->http->get('/v1/analytics/geo', [
            'days' => $params['days'] ?? null,
            'eventType' => $params['eventType'] ?? null,
        ]);
    }

    /**
     * Get organization-level device analytics.
     *
     * @param array{days?: int, eventType?: string} $params
     *
     * @return array<string, mixed>
     */
    public function devices(array $params = []): array
    {
        return $this->http->get('/v1/analytics/devices', [
            'days' => $params['days'] ?? null,
            'eventType' => $params['eventType'] ?? null,
        ]);
    }

    /**
     * Get campaign-level geo analytics.
     *
     * @param array{eventType?: string} $params
     *
     * @return array<string, mixed>
     */
    public function campaignGeo(string $campaignId, array $params = []): array
    {
        return $this->http->get("/v1/campaigns/{$campaignId}/analytics/geo", [
            'eventType' => $params['eventType'] ?? null,
        ]);
    }

    /**
     * Get campaign-level device analytics.
     *
     * @param array{eventType?: string} $params
     *
     * @return array<string, mixed>
     */
    public function campaignDevices(string $campaignId, array $params = []): array
    {
        return $this->http->get("/v1/campaigns/{$campaignId}/analytics/devices", [
            'eventType' => $params['eventType'] ?? null,
        ]);
    }
}
