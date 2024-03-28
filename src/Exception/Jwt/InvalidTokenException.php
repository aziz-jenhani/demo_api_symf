<?php

namespace App\Exception\Jwt;

use App\Exception\UnauthorizedException;

class InvalidTokenException extends UnauthorizedException
{
    public function __construct(string $message = 'Invalid token')
    {
        parent::__construct($message);
    }

    public function getAppCode(): ?string
    {
        return 'invalid_token';
    }
}
