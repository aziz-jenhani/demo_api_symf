<?php

namespace App\Exception;

use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Response;

class NotFoundException extends AbstractException
{
    public function __construct(private string $entity, string $message = 'Not found')
    {
        parent::__construct($message);
    }

    public function getStatus(): int
    {
        return Response::HTTP_NOT_FOUND;
    }

    public function getType(): string
    {
        return 'not_found_error';
    }

    public function getSeverity(): string
    {
        return LogLevel::INFO;
    }

    public function getEntity(): string
    {
        return $this->entity;
    }
}
