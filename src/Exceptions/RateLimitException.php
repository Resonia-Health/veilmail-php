<?php

declare(strict_types=1);

namespace VeilMail\Exceptions;

/**
 * Thrown when the rate limit is exceeded (429).
 */
class RateLimitException extends VeilMailException
{
    private ?int $retryAfter;

    public function __construct(
        string $message = 'Rate limit exceeded',
        ?int $retryAfter = null,
        ?string $errorCode = null,
        ?array $details = null,
    ) {
        parent::__construct($message, $errorCode, 429, $details);
        $this->retryAfter = $retryAfter;
    }

    public function getRetryAfter(): ?int
    {
        return $this->retryAfter;
    }
}
