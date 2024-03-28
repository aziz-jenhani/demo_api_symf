<?php

namespace App\Exception;

use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Response;

abstract class ApplicationException extends AbstractException
{
    public function __construct(string $message = '', \Throwable $previous = null)
    {
        parent::__construct($message, $previous);
    }

    public function getStatus(): int
    {
        return Response::HTTP_CONFLICT;
    }

    public function getType(): string
    {
        return 'application_error';
    }

    public function getSeverity(): string
    {
        return LogLevel::INFO;
    }
}
