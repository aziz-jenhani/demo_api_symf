<?php

namespace App\Controller;

use App\Dto\User\CreateUser;
use App\Helper\ControllerTrait;
use App\Search\SearchService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use function OpenApi\Attributes\JsonContent;

#[Route('/api/security')]
#[Tag(name: 'Auth')]
class SecurityController extends AbstractController
{
    use ControllerTrait;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private SearchService $searchService,
        private UserService $userService
    ) {
    }
    #[Route('/register', methods: ['POST'])]
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
                new OA\Property(property: 'email', type: 'string'),
                new OA\Property(property: 'firstname', type: 'string'),
                new OA\Property(property: 'lastname', type: 'string'),
                new OA\Property(property: 'password', type: 'string'),
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
  /*  #[Route('/refresh-token', methods: ['POST'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the access token and the refresh token'
    ), OA\Response(
        response: 401,
        description: 'Bad credentials'
    ), OA\RequestBody(
        description: 'Credentials containing the refresh token.',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'refresh_token', type: 'string'),
            ]
        )
    )]
    #[Security(name: 'Bearer')]
    public function refreshTokenAction(Request $request): Response
    {
        $user = $this->getUser(); // Obtenez l'utilisateur actuellement authentifié

        // Générez un nouveau token JWT pour cet utilisateur
        $token = $this->userService->create($user);

        // Retournez le nouveau token dans la réponse
        return new Response(['token' => $token]);
    }*/
    #[Route('/refresh-token', methods: ['POST'])]
      #[OA\Response(
          response: 200,
          description: "Returns the access token and the refresh token"
      ), OA\Response(
          response: 401,
          description: "Bad credentials"
      ),OA\RequestBody(
          description: "Credentials containing the refresh token.",
          content: new OA\JsonContent(
              properties: [
                  new OA\Property(property: 'refresh_token"', type:'string'),
              ]
          )
      )]
      #[Security(name: 'Bearer')]
      public function refreshTokenAction(Request $request)
      {
          $jwtRefreshTokenService = $this->get('gesdinet.jwtrefreshtoken');
          // Assuming `refresh()` method exists in the `gesdinet.jwtrefreshtoken` service
          return $jwtRefreshTokenService->refresh($request);    }

}