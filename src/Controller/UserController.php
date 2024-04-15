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
use OpenApi\Attributes\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

#[Tag(name: 'Users')]
class UserController extends AbstractController
{
    use ControllerTrait;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private SearchService $searchService,
        private UserService $userService
    ) {
    }

    #[Route('/api/me', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Renvoie la liste des clients ',
        content: new Model(type: User::class)
    )]
    #[Security(name: 'Bearer')]

    public function me(): Response
    {
        return $this->okResponse($this->getUser());
    }

    #[Route('/api/users', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Renvoie la liste des clients ',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: User::class)))
    )]
    #[Security(name: 'Bearer')]

    public function list(SearchUser $searchUser): Response
    {
        $result = $this->searchService->getSearchResults(User::class, $searchUser);
        return $this->okCollectionResponse($result);
    }

    #[Route('/api/users', methods: ['POST'])]
    #[OA\Response(
        response: 200,
        description: 'Add user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: CreateUser::class))
        )
    ), OA\RequestBody(
        description: 'Add user',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'email', type:'string'),
                new OA\Property(property: 'firstname', type:'string'),
                new OA\Property(property: 'lastname', type:'string'),
                new OA\Property(property: 'password', type:'string'),


            ]
        )
    )]
    #[Security(name: 'Bearer')]

    public function create(CreateUser $createUser): Response
    {
        $user = $this->userService->create($createUser);
        $this->entityManager->flush();
        return $this->createdResponse($user);
    }

    #[Route('/api/users/{id}', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Voici votre utilisateur ',
        content: new Model(type: User::class)
    )]
    #[Security(name: 'Bearer')]

    public function get(string $id): Response
    {
        $user = $this->userService->get($id);
        return $this->okResponse($user);
    }

    #[Route('/api/users/{id}', methods: ['PUT', 'PATCH'])]
    #[OA\Response(
        response: 200,
        description: 'Modifie un utilisateur',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'id', type: 'string'),
                new OA\Property(property: 'email', type: 'string'),
                new OA\Property(property: 'firstname', type: 'string'),
                new OA\Property(property: 'lastname', type: 'string')
            ],
            type: 'object'
        )
    )]
    #[OA\RequestBody(
        description: 'Données pour mettre à jour l\'utilisateur',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'email', type: 'string'),
                new OA\Property(property: 'firstname', type: 'string'),
                new OA\Property(property: 'lastname', type: 'string')
            ]
        )
    )]
    #[Security(name: 'Bearer')]

    public function update(string $id, UpdateUser $updateUser): Response
    {
        $user = $this->userService->update($id, $updateUser);
        $this->entityManager->flush();
        return $this->okResponse($user);
    }

    #[Route('/api/users/{id}', methods: ['DELETE'])]
    #[OA\Response(
        response: 204,
        description: 'Utilisateur Supprimer'
    )]
    #[Security(name: 'Bearer')]

    public function delete(string $id): Response
    {
        $this->userService->delete($id);
        $this->entityManager->flush();
        return $this->noContentResponse();
    }
}
