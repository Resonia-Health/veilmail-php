<?php

declare(strict_types=1);

namespace VeilMail\Exceptions;

/**
 * Thrown when the requested resource is not found (404).
 */
class NotFoundException extends VeilMailException
{
    public function __construct(string $message = 'Resource not found', ?string $errorCode = null, ?array $details = null)
    {
        parent::__construct($message, $errorCode, 404, $details);
    }
}
