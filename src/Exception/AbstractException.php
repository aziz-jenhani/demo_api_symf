<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

abstract class AbstractException extends \Exception
{
    /**
     * @param string $message
     * @param \Throwable|null $previous
     */
    public function __construct(string $message = '', \Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }

    public function getStatus(): int
    {
        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    abstract public function getType(): string;

    abstract public function getSeverity(): string;

    public function getAppCode(): ?string
    {
        return null;
    }
}
