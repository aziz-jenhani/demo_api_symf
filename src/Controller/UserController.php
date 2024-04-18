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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Tag(name: 'Users')]
class UserController extends AbstractController
{
    use ControllerTrait;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private SearchService $searchService,
        private UserService $userService,
        private UserPasswordHasherInterface $passwordHasher
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

    #[Route('/api/users/me', methods: ['PUT'])]
    #[OA\Response(
        response: 200,
        description: 'Mise à jour des informations de l\'utilisateur actuel',
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
        description: 'Données pour mettre à jour les informations de l\'utilisateur',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'email', type: 'string'),
                new OA\Property(property: 'firstname', type: 'string'),
                new OA\Property(property: 'lastname', type: 'string')
            ]
        )
    )]
    #[Security(name: 'Bearer')]
    public function putMe(UpdateUser $updateUser): Response
    {
        $user = $this->userService->update($this->getUser()->getId(), $updateUser);
        $this->entityManager->flush();
        return $this->okResponse($user);
    }

    #[Route('/api/users', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Renvoie la liste des clients avec pagination',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: User::class))
        )
    ),OA\Parameter(
        name: 'page',
        description: 'Affichage des liste des clients avec pagination',
        in: 'query',
        required: false,
        schema: new OA\Schema(type:"integer", default:1)
    ),OA\Parameter(
        name: "limit",
        description: "Nombre d'utilisateurs par page",
        in: "query",
        required: false,
        schema: new OA\Schema(type:"integer", default:10)
        )]
    #[IsGranted('ROLE_ADMIN')]
    #[Security(name: 'Bearer')]
    public function list(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        $slug = $request->query->get('slug');

        // Set default values if not provided
        $page = max(1, $page); // Ensure page is at least 1
        $limit = max(1, $limit); // Ensure limit is at least 1

        $searchUser = new SearchUser();
        $searchUser->setSlug($slug);
        $searchUser->setPage($page);
        $searchUser->setLimit($limit);

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
    #[IsGranted('ROLE_ADMIN')]
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

    #[Route('/api/users/{id}', methods: ['PUT'])]
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
    #[IsGranted('ROLE_ADMIN')]
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
    #[IsGranted('ROLE_ADMIN')]
    #[Security(name: 'Bearer')]

    public function delete(string $id): Response
    {
        $this->userService->delete($id);
        $this->entityManager->flush();
        return $this->noContentResponse();
    }
    #[Route('/api/users/me/password', methods: ['PUT'])]
    #[OA\Response(
        response: 200,
        description: 'Mot de passe mis à jour avec succès'
    )]
    #[OA\RequestBody(
        description: 'Nouveau mot de passe de l\'utilisateur',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'password', type: 'string')
            ]
        )
    )]
    #[Security(name: 'Bearer')]
    public function putPassword(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $user = $this->getUser();
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $data['password']
        );
        $user->setPassword($hashedPassword);

        $this->entityManager->flush();

        return $this->okResponse("Mot de passe mis à jour avec succès");
    }
}
