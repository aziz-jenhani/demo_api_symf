<?php

namespace App\Exception\Jwt;

use App\Exception\UnauthorizedException;

class ExpiredTokenException extends UnauthorizedException
{
    public function __construct(string $message = 'Expired token')
    {
        parent::__construct($message);
    }

    public function getAppCode(): ?string
    {
        return 'expired_token';
    }
}
