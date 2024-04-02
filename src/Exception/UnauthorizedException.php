<?php

namespace App\Exception;

use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Response;

class UnauthorizedException extends AbstractException
{
    public function __construct(string $message = 'Unauthorized')
    {
        parent::__construct($message);
    }

    public function getStatus(): int
    {
        return Response::HTTP_UNAUTHORIZED;
    }

    public function getType(): string
    {
        return 'authentication_error';
    }

    public function getSeverity(): string
    {
        return LogLevel::INFO;
    }
}
