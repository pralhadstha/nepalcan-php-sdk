<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Exceptions;

class NotFoundException extends ApiException
{
    public function __construct(string $message = 'Not found.', ?\Throwable $previous = null)
    {
        parent::__construct($message, 404, [], $previous);
    }
}
