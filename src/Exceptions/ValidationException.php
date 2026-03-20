<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Exceptions;

class ValidationException extends ApiException
{
    private array $errors;

    public function __construct(array $errors, string $message = 'Validation failed', ?\Throwable $previous = null)
    {
        $this->errors = $errors;

        parent::__construct($message, 400, $errors, $previous);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
