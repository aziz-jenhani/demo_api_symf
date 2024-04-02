<?php

namespace App\Fixture;

use Symfony\Component\Uid\NilUuid;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;

class Provider
{
    public function uuid(): Uuid
    {
        return new UuidV7();
    }

    public function uuid5(string $name): Uuid
    {
        return Uuid::v5(new NilUuid(), $name);
    }

    public function argon(string $plainPassword): string
    {
        return password_hash($plainPassword, PASSWORD_ARGON2I);
    }
}
