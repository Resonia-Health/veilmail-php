<?php

declare(strict_types=1);

namespace VeilMail\Exceptions;

/**
 * Thrown when access is denied due to insufficient permissions (403).
 */
class ForbiddenException extends VeilMailException
{
    public function __construct(string $message = 'Access denied', ?string $errorCode = null, ?array $details = null)
    {
        parent::__construct($message, $errorCode, 403, $details);
    }
}
