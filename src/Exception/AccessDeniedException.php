<?php

namespace App\Exception;

use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Response;

class AccessDeniedException extends AbstractException
{
    public function __construct(string $message = 'Access Denied')
    {
        parent::__construct($message);
    }

    public function getStatus(): int
    {
        return Response::HTTP_FORBIDDEN;
    }

    public function getType(): string
    {
        return 'permission_error';
    }

    public function getSeverity(): string
    {
        return LogLevel::INFO;
    }
}
