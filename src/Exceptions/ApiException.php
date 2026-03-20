<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Exceptions;

use Exception;

class ApiException extends Exception
{
    private int $statusCode;
    private array $errorBody;

    public function __construct(string $message, int $statusCode = 0, array $errorBody = [], ?\Throwable $previous = null)
    {
        $this->statusCode = $statusCode;
        $this->errorBody = $errorBody;

        parent::__construct($message, $statusCode, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrorBody(): array
    {
        return $this->errorBody;
    }
}
