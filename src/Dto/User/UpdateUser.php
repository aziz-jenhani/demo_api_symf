<?php

namespace App\Dto\User;

use App\Dto\BodyContentDto;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateUser implements BodyContentDto
{
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Email]
    public ?string $email = null;

    #[Assert\NotBlank(allowNull: true)]
    public ?string $firstname = null;

    #[Assert\NotBlank(allowNull: true)]
    public ?string $lastname = null;
}
