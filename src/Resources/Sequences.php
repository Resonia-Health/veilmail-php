<?php

declare(strict_types=1);

namespace VeilMail\Resources;

use VeilMail\HttpClient;

/**
 * Automation sequence management.
 */
class Sequences
{
    public function __construct(private readonly HttpClient $http) {}

    /**
     * Create a new automation sequence.
     *
     * @param array{
     *     name: string,
     *     audienceId: string,
     *     triggerType: string,
     *     description?: string,
     *     triggerConfig?: array<string, mixed>,
     * } $params
     *
     * @return array<string, mixed>
     */
    public function create(array $params): array
    {
        return $this->http->post('/v1/sequences', $params);
    }

    /**
     * List all automation sequences.
     *
     * @param array{limit?: int, cursor?: string} $params
     *
     * @return array<string, mixed>
     */
    public function list(array $params = []): array
    {
        return $this->http->get('/v1/sequences', [
            'limit' => $params['limit'] ?? null,
            'cursor' => $params['cursor'] ?? null,
        ]);
    }

    /**
     * Get a single sequence by ID.
     *
     * @return array<string, mixed>
     */
    public function get(string $id): array
    {
        return $this->http->get("/v1/sequences/{$id}");
    }

    /**
     * Update a sequence (only DRAFT or PAUSED).
     *
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     */
    public function update(string $id, array $params): array
    {
        return $this->http->put("/v1/sequences/{$id}", $params);
    }

    /**
     * Delete a sequence (only DRAFT).
     */
    public function delete(string $id): void
    {
        $this->http->delete("/v1/sequences/{$id}");
    }

    /**
     * Activate a sequence.
     *
     * @return array<string, mixed>
     */
    public function activate(string $id): array
    {
        return $this->http->post("/v1/sequences/{$id}/activate");
    }

    /**
     * Pause an active sequence.
     *
     * @return array<string, mixed>
     */
    public function pause(string $id): array
    {
        return $this->http->post("/v1/sequences/{$id}/pause");
    }

    /**
     * Archive a sequence.
     *
     * @return array<string, mixed>
     */
    public function archive(string $id): array
    {
        return $this->http->post("/v1/sequences/{$id}/archive");
    }

    /**
     * Add a step to a sequence.
     *
     * @param array{
     *     position: int,
     *     type: string,
     *     subject?: string,
     *     html?: string,
     *     text?: string,
     *     templateId?: string,
     *     delayAmount?: int,
     *     delayUnit?: string,
     *     conditionType?: string,
     *     conditionConfig?: array<string, mixed>,
     * } $params
     *
     * @return array<string, mixed>
     */
    public function addStep(string $sequenceId, array $params): array
    {
        return $this->http->post("/v1/sequences/{$sequenceId}/steps", $params);
    }

    /**
     * Update a sequence step.
     *
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     */
    public function updateStep(string $sequenceId, string $stepId, array $params): array
    {
        return $this->http->put("/v1/sequences/{$sequenceId}/steps/{$stepId}", $params);
    }

    /**
     * Delete a sequence step.
     */
    public function deleteStep(string $sequenceId, string $stepId): void
    {
        $this->http->delete("/v1/sequences/{$sequenceId}/steps/{$stepId}");
    }

    /**
     * Reorder sequence steps.
     *
     * @param array<int, array{id: string, position: int}> $steps
     */
    public function reorderSteps(string $sequenceId, array $steps): void
    {
        $this->http->post("/v1/sequences/{$sequenceId}/steps/reorder", ['steps' => $steps]);
    }

    /**
     * Enroll subscribers into a sequence.
     *
     * @param string[] $subscriberIds
     *
     * @return array<string, mixed>
     */
    public function enroll(string $sequenceId, array $subscriberIds): array
    {
        return $this->http->post("/v1/sequences/{$sequenceId}/enroll", [
            'subscriberIds' => $subscriberIds,
        ]);
    }

    /**
     * List enrollments for a sequence.
     *
     * @param array{limit?: int, cursor?: string} $params
     *
     * @return array<string, mixed>
     */
    public function listEnrollments(string $sequenceId, array $params = []): array
    {
        return $this->http->get("/v1/sequences/{$sequenceId}/enrollments", [
            'limit' => $params['limit'] ?? null,
            'cursor' => $params['cursor'] ?? null,
        ]);
    }

    /**
     * Remove an enrollment from a sequence.
     */
    public function removeEnrollment(string $sequenceId, string $enrollmentId): void
    {
        $this->http->delete("/v1/sequences/{$sequenceId}/enrollments/{$enrollmentId}");
    }
}
