<?php

declare(strict_types=1);

namespace VeilMail\Exceptions;

/**
 * Thrown when request validation fails (400).
 */
class ValidationException extends VeilMailException
{
    public function __construct(string $message = 'Validation failed', ?string $errorCode = null, ?array $details = null)
    {
        parent::__construct($message, $errorCode, 400, $details);
    }
}
