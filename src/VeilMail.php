<?php

declare(strict_types=1);

namespace VeilMail;

use InvalidArgumentException;
use VeilMail\Resources\Analytics;
use VeilMail\Resources\Audiences;
use VeilMail\Resources\Campaigns;
use VeilMail\Resources\Domains;
use VeilMail\Resources\Emails;
use VeilMail\Resources\Feeds;
use VeilMail\Resources\Forms;
use VeilMail\Resources\Properties;
use VeilMail\Resources\Sequences;
use VeilMail\Resources\Templates;
use VeilMail\Resources\Topics;
use VeilMail\Resources\Webhooks;

/**
 * Veil Mail API client.
 *
 * @example
 * ```php
 * $client = new VeilMail\VeilMail('veil_live_xxxxx');
 *
 * $email = $client->emails->send([
 *     'from' => 'hello@yourdomain.com',
 *     'to' => 'user@example.com',
 *     'subject' => 'Hello from PHP!',
 *     'html' => '<h1>Welcome!</h1>',
 * ]);
 * ```
 */
class VeilMail
{
    public readonly Emails $emails;
    public readonly Domains $domains;
    public readonly Templates $templates;
    public readonly Audiences $audiences;
    public readonly Campaigns $campaigns;
    public readonly Webhooks $webhooks;
    public readonly Topics $topics;
    public readonly Properties $properties;
    public readonly Sequences $sequences;
    public readonly Feeds $feeds;
    public readonly Forms $forms;
    public readonly Analytics $analytics;

    /**
     * Create a new Veil Mail client.
     *
     * @param string      $apiKey  Your API key (must start with veil_live_ or veil_test_)
     * @param string|null $baseUrl Custom API base URL (default: https://api.veilmail.xyz)
     * @param int|null    $timeout Request timeout in seconds (default: 30)
     *
     * @throws InvalidArgumentException If the API key format is invalid
     */
    public function __construct(string $apiKey, ?string $baseUrl = null, ?int $timeout = null)
    {
        if (!str_starts_with($apiKey, 'veil_live_') && !str_starts_with($apiKey, 'veil_test_')) {
            throw new InvalidArgumentException(
                'Invalid API key format. Key must start with "veil_live_" or "veil_test_".'
            );
        }

        $http = new HttpClient($apiKey, $baseUrl, $timeout);

        $this->emails = new Emails($http);
        $this->domains = new Domains($http);
        $this->templates = new Templates($http);
        $this->audiences = new Audiences($http);
        $this->campaigns = new Campaigns($http);
        $this->webhooks = new Webhooks($http);
        $this->topics = new Topics($http);
        $this->properties = new Properties($http);
        $this->sequences = new Sequences($http);
        $this->feeds = new Feeds($http);
        $this->forms = new Forms($http);
        $this->analytics = new Analytics($http);
    }
}
