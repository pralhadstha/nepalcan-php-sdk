<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Exceptions;

class AuthenticationException extends ApiException
{
    public function __construct(string $message = 'Authentication credentials were not provided.', ?\Throwable $previous = null)
    {
        parent::__construct($message, 401, [], $previous);
    }
}
