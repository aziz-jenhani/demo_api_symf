<?php

namespace App\Exception;

use Psr\Log\LogLevel;

class InternalException extends AbstractException
{
    public function __construct(\Throwable $previous = null, string $message = 'Internal error')
    {
        parent::__construct($message, $previous);
    }

    public function getType(): string
    {
        return 'internal_error';
    }

    public function getSeverity(): string
    {
        return LogLevel::ERROR;
    }
}
