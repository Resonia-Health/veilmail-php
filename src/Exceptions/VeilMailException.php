<?php

declare(strict_types=1);

namespace VeilMail\Exceptions;

use Exception;

/**
 * Base exception for all Veil Mail API errors.
 */
class VeilMailException extends Exception
{
    protected ?string $errorCode;
    protected ?int $statusCode;
    protected ?array $details;

    public function __construct(
        string $message = 'An error occurred',
        ?string $errorCode = null,
        ?int $statusCode = null,
        ?array $details = null,
    ) {
        parent::__construct($message);
        $this->errorCode = $errorCode;
        $this->statusCode = $statusCode;
        $this->details = $details;
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    public function getDetails(): ?array
    {
        return $this->details;
    }
}
