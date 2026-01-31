<?php

declare(strict_types=1);

namespace VeilMail\Exceptions;

/**
 * Thrown when PII is detected in email content (422).
 */
class PiiDetectedException extends VeilMailException
{
    /** @var string[] */
    private array $piiTypes;

    /**
     * @param string[] $piiTypes
     */
    public function __construct(
        string $message = 'PII detected',
        array $piiTypes = [],
        ?string $errorCode = null,
        ?array $details = null,
    ) {
        parent::__construct($message, $errorCode, 422, $details);
        $this->piiTypes = $piiTypes;
    }

    /**
     * @return string[]
     */
    public function getPiiTypes(): array
    {
        return $this->piiTypes;
    }
}
