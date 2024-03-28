<?php

namespace App\Dto\User;

use App\Dto\BodyContentDto;
use Symfony\Component\Validator\Constraints as Assert;

class CreateUser implements BodyContentDto
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public ?string $email = null;

    #[Assert\NotBlank]
    public ?string $firstname = null;

    #[Assert\NotBlank]
    public ?string $lastname = null;

    #[Assert\NotBlank]
    public ?string $password = null;
}
