<?php

declare(strict_types=1);

namespace VeilMail\Exceptions;

/**
 * Thrown for server errors (5xx).
 */
class ServerException extends VeilMailException
{
    public function __construct(string $message = 'Server error', ?string $errorCode = null, ?int $statusCode = 500, ?array $details = null)
    {
        parent::__construct($message, $errorCode, $statusCode, $details);
    }
}
