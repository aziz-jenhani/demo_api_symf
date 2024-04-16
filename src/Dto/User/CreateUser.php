<?php

namespace App\Dto\User;

use App\Dto\BodyContentDto;
use Symfony\Component\Validator\Constraints as Assert;

class CreateUser implements BodyContentDto
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public ?string $email = null;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): void
    {
        $this->lastname = $lastname;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): void
    {
        $this->firstname = $firstname;
    }

    #[Assert\NotBlank]
    public ?string $firstname = null;

    #[Assert\NotBlank]
    public ?string $lastname = null;

    #[Assert\NotBlank]
    public ?string $password = null;
}
