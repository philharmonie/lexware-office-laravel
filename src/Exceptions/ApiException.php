<?php

declare(strict_types=1);

namespace PhilHarmonie\LexOffice\Exceptions;

use Exception;

final class ApiException extends Exception
{
    public function __construct(
        string $message,
        public readonly int $statusCode,
        public readonly array $response = [],
        ?Exception $previous = null
    ) {
        parent::__construct($message, $statusCode, $previous);
    }

    public function getResponse(): array
    {
        return $this->response;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
