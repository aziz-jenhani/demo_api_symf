<?php

namespace App\Controller;

use App\Dto\User\CreateUser;
use App\Entity\RefreshToken;
use App\Entity\User;
use App\Helper\ControllerTrait;
use App\Search\SearchService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/api/security')]
#[Tag(name: 'Auth')]
class SecurityController extends AbstractController
{
    use ControllerTrait;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private SearchService $searchService,
        private UserService $userService,
        private JWTTokenManagerInterface $jwtManager,
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
    #[Route('/refresh-token', methods: ['POST'])]
      #[OA\RequestBody(
          description: "Credentials containing the refresh token.",
          content: new OA\JsonContent(
              properties: [
                  new OA\Property(property: 'refresh_token', type:'string'),
              ]
          )
      )]
      #[Security(name: 'Bearer')]
    public function refreshToken(Request $request): Response
    {
        // Retrieve the refresh token from the request body
        $content = json_decode($request->getContent(), true);
        $refreshToken = $content['refresh_token'] ?? null;

        if (!$refreshToken) {
            throw new AccessDeniedException('Refresh token not provided.');
        }
        $isValid = $this->isValidRefreshToken($refreshToken);
        if (!$isValid) {
            throw new AccessDeniedException('Refresh token not provided.');
        }
        $user = $this->getUserFromRefreshToken($refreshToken);
        if (!$user instanceof UserInterface) {
            throw new AccessDeniedException('Invalid user.');
        }
        $accessToken = $this->jwtManager->create($user);
        return new JsonResponse(['token' => $accessToken]);
    }
    private function isValidRefreshToken(string $refreshToken): bool
    {
        $refreshTokenRepository = $this->entityManager->getRepository(RefreshToken::class);
        $refreshTokenUserEmail = $refreshTokenRepository->findOneBy(['refreshToken' => $refreshToken]);
        return $refreshTokenUserEmail !== null;
    }
    private function getUserFromRefreshToken(string $refreshToken): ?UserInterface
    {
        $refreshTokenRepository = $this->entityManager->getRepository(RefreshToken::class);
        $refreshTokenUserEmail = $refreshTokenRepository->findOneBy(['refreshToken' => $refreshToken]);

        // Assuming RefreshToken entity has an 'email' property
        if ($refreshTokenUserEmail) {
            $refreshTokenUser = $this->entityManager->getRepository(User::class)->findOneByEmail($refreshTokenUserEmail->getUsername());
            return $refreshTokenUser;
        }

        return null;
    }
}
