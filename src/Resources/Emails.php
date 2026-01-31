<?php

declare(strict_types=1);

namespace VeilMail\Resources;

use VeilMail\HttpClient;

/**
 * Email sending and management.
 */
class Emails
{
    public function __construct(private readonly HttpClient $http) {}

    /**
     * Send a single email.
     *
     * @param array{
     *     from: string,
     *     to: string|string[],
     *     subject: string,
     *     html?: string,
     *     text?: string,
     *     cc?: string|string[],
     *     bcc?: string|string[],
     *     replyTo?: string,
     *     templateId?: string,
     *     templateData?: array<string, mixed>,
     *     scheduledFor?: string,
     *     tags?: string[],
     *     metadata?: array<string, mixed>,
     *     idempotencyKey?: string,
     *     attachments?: array<array{filename: string, content?: string, url?: string, contentType: string, contentId?: string}>,
     *     topicId?: string,
     *     type?: string,
     *     unsubscribeUrl?: string,
     *     headers?: array<string, string>,
     * } $params
     *
     * @return array<string, mixed> The created email
     */
    public function send(array $params): array
    {
        $to = $params['to'] ?? [];
        $body = [
            'from' => $params['from'],
            'to' => is_array($to) ? $to : [$to],
            'subject' => $params['subject'],
            'html' => $params['html'] ?? null,
            'text' => $params['text'] ?? null,
            'cc' => isset($params['cc']) ? (is_array($params['cc']) ? $params['cc'] : [$params['cc']]) : null,
            'bcc' => isset($params['bcc']) ? (is_array($params['bcc']) ? $params['bcc'] : [$params['bcc']]) : null,
            'replyTo' => $params['replyTo'] ?? null,
            'templateId' => $params['templateId'] ?? null,
            'templateData' => $params['templateData'] ?? null,
            'scheduledFor' => $params['scheduledFor'] ?? null,
            'tags' => $params['tags'] ?? null,
            'metadata' => $params['metadata'] ?? null,
            'idempotencyKey' => $params['idempotencyKey'] ?? null,
            'attachments' => $params['attachments'] ?? null,
            'topicId' => $params['topicId'] ?? null,
            'type' => $params['type'] ?? null,
            'unsubscribeUrl' => $params['unsubscribeUrl'] ?? null,
            'headers' => $params['headers'] ?? null,
        ];

        return $this->http->post('/v1/emails', $body);
    }

    /**
     * Send a batch of up to 100 emails.
     *
     * @param array<array<string, mixed>> $emails Array of email params (same format as send)
     *
     * @return array<string, mixed> Batch result with per-email status
     */
    public function sendBatch(array $emails): array
    {
        return $this->http->post('/v1/emails/batch', ['emails' => $emails]);
    }

    /**
     * List emails with optional filters.
     *
     * @param array{
     *     limit?: int,
     *     cursor?: string,
     *     status?: string,
     *     tag?: string,
     *     after?: string,
     *     before?: string,
     * } $params
     *
     * @return array<string, mixed> Paginated list with data, hasMore, nextCursor
     */
    public function list(array $params = []): array
    {
        return $this->http->get('/v1/emails', [
            'limit' => $params['limit'] ?? null,
            'cursor' => $params['cursor'] ?? null,
            'status' => $params['status'] ?? null,
            'tag' => $params['tag'] ?? null,
            'after' => $params['after'] ?? null,
            'before' => $params['before'] ?? null,
        ]);
    }

    /**
     * Get a single email by ID.
     *
     * @return array<string, mixed> Email details
     */
    public function get(string $id): array
    {
        return $this->http->get("/v1/emails/{$id}");
    }

    /**
     * Cancel a scheduled email.
     *
     * @return array<string, mixed> Cancellation result
     */
    public function cancel(string $id): array
    {
        return $this->http->post("/v1/emails/{$id}/cancel");
    }

    /**
     * Reschedule a scheduled email.
     *
     * @param array{scheduledFor: string} $params
     *
     * @return array<string, mixed> Updated email
     */
    public function update(string $id, array $params): array
    {
        return $this->http->patch("/v1/emails/{$id}", $params);
    }

    /**
     * Get tracked link analytics for a specific email.
     *
     * @param array{limit?: int, sort?: string, order?: string} $params
     *
     * @return array<string, mixed> Link analytics data
     */
    public function links(string $id, array $params = []): array
    {
        return $this->http->get("/v1/emails/{$id}/links", [
            'limit' => $params['limit'] ?? null,
            'sort' => $params['sort'] ?? null,
            'order' => $params['order'] ?? null,
        ]);
    }
}
