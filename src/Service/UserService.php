<?php

namespace App\Service;

use App\Dto\User\CreateUser;
use App\Dto\User\UpdateUser;
use App\Entity\User;
use App\Mapper\UserMapper;
use App\Repository\UserRepository;
use App\Exception\NotFoundException;

class UserService
{
    public function __construct(
        private UserRepository $userRepository,
        private UserMapper $userMapper
    ) {
    }

    public function get(string $id): User
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            throw new NotFoundException(User::class);
        }

        return $user;
    }

    public function create(CreateUser $createUser): User
    {
        $user = $this->userMapper->mapCreateUserToUser($createUser, new User());
        $this->userRepository->save($user);

        return $user;
    }

    public function update(string $id, UpdateUser $updateUser): User
    {
        $user = $this->get($id);
        $user = $this->userMapper->mapUpdateUserToUser($updateUser, $user);
        $this->userRepository->save($user);

        return $user;
    }

    public function delete(string $id): void
    {
        $user = $this->get($id);
        $this->userRepository->remove($user);
    }
}
