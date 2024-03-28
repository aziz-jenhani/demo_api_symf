<?php

namespace App\Mapper;

use App\Dto\User\CreateUser;
use App\Dto\User\UpdateUser;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserMapper
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function mapCreateUserToUser(CreateUser $createUser, User $user): User
    {
        $user->setFirstname($createUser->firstname);
        $user->setLastname($createUser->lastname);
        $user->setEmail($createUser->email);
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $createUser->password
        );
        $user->setPassword($hashedPassword);

        return $user;
    }

    public function mapUpdateUserToUser(UpdateUser $updateUser, User $user): User
    {
        if ($updateUser->firstname) {
            $user->setFirstname($updateUser->firstname);
        }

        if ($updateUser->lastname) {
            $user->setLastname($updateUser->lastname);
        }

        if ($updateUser->email) {
            $user->setEmail($updateUser->email);
        }

        return $user;
    }
}
