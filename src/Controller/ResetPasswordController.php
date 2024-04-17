<?php

namespace App\Controller;

use App\Dto\User\CreateUser;
use App\Entity\User;
use App\Exception\NotFoundException;
use App\Service\ResetPasswordService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use OpenApi\Attributes\Tag;
use OpenApi\Attributes as OA;

#[Route('/api/security/reset-password')]
#[Tag(name: 'Auth')]
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public function __construct(private ResetPasswordService $resetPasswordService)
    {
    }

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return JsonResponse
     * @throws NotFoundException
     */
    #[Route('/', name: 'app_forgot_password_request', methods: ['POST'])]
    #[OA\Response(
        response: 200,
        description: 'Reset email sent',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string'),
                new OA\Property(property: 'token', type: 'string')

            ]
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Bad Request',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'email', type: 'string'),
            ]
        )
    )]
    #[OA\RequestBody(
        required:true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'email', type: 'string')
            ]
        )
    )]
    public function request(Request $request, TranslatorInterface $translator): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        if (!isset($requestData['email'])) {
            return new JsonResponse(['message' => 'Email is required'], Response::HTTP_BAD_REQUEST);
        }

        $email = $requestData['email'];
        $resetToken = $this->resetPasswordService->sendResetPasswordEmail($email);

        return new JsonResponse(['message' => 'Reset email sent', 'resetToken' => $resetToken]);
    }

    #[Route('/check', name: 'app_check_email', methods: ['POST'])]
    #[OA\Response(
        response: 200,
        description: 'Voici votre utilisateur ',
        content: new Model(type: User::class)
    )]
    public function checkEmail(): JsonResponse
    {
        $resetToken = $this->getTokenFromSession() ?? $this->resetPasswordHelper->generateFakeResetToken();

        return new JsonResponse(['resetToken' => $resetToken]);
    }

    #[Route('/reset/{token}', name: 'app_reset_password', methods: ['POST'])]
    #[OA\Response(
        response: 200,
        description: 'Password updated successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string'),
            ]
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Bad Request',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'email', type: 'string'),
            ]
        )
    )]
    #[OA\RequestBody(
        required:true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'new_password', type: 'string')
            ]
        )
    )]
    public function reset(Request $request, string $token): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $newPassword = $requestData['new_password'] ?? null;

        if (!$newPassword) {
            return new JsonResponse(['message' => 'New password is required'], Response::HTTP_BAD_REQUEST);
        }

        $this->resetPasswordService->validateTokenAndResetPassword($token, $newPassword);

        return new JsonResponse(['message' => 'Password updated successfully']);
    }
}
