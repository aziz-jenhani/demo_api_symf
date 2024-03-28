<?php

namespace App\Controller;

use App\Dto\User\CreateUser;
use App\Dto\User\SearchUser;
use App\Dto\User\UpdateUser;
use App\Entity\User;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ControllerTrait;
use App\Search\SearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    use ControllerTrait;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private SearchService $searchService,
        private UserService $userService
    ) {
    }

    #[Route('/me', methods: ['GET'])]
    public function me(): Response
    {
        return $this->okResponse($this->getUser());
    }

    #[Route('/users', methods: ['GET'])]
    public function list(SearchUser $searchUser): Response
    {
        $result = $this->searchService->getSearchResults(User::class, $searchUser);
        return $this->okCollectionResponse($result);
    }

    #[Route('/users', methods: ['POST'])]
    public function create(CreateUser $createUser): Response
    {
        $user = $this->userService->create($createUser);
        $this->entityManager->flush();
        return $this->createdResponse($user);
    }

    #[Route('/users/{id}', methods: ['GET'])]
    public function get(string $id): Response
    {
        $user = $this->userService->get($id);
        return $this->okResponse($user);
    }

    #[Route('/users/{id}', methods: ['PUT', 'PATCH'])]
    public function update(string $id, UpdateUser $updateUser): Response
    {
        $user = $this->userService->update($id, $updateUser);
        $this->entityManager->flush();
        return $this->okResponse($user);
    }

    #[Route('/users/{id}', methods: ['DELETE'])]
    public function delete(string $id): Response
    {
        $this->userService->delete($id);
        $this->entityManager->flush();
        return $this->noContentResponse();
    }
}
