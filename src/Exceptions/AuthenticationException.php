<?php

declare(strict_types=1);

namespace VeilMail\Exceptions;

/**
 * Thrown when the API key is invalid or missing (401).
 */
class AuthenticationException extends VeilMailException
{
    public function __construct(string $message = 'Invalid API key', ?string $errorCode = null, ?array $details = null)
    {
        parent::__construct($message, $errorCode, 401, $details);
    }
}
