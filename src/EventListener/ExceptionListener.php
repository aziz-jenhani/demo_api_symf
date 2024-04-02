<?php

namespace App\EventListener;

use App\Exception\AbstractException;
use App\Exception\ValidationException;
use App\Helper\LoggerTrait;
use App\Helper\TranslatorTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExceptionListener
{
    use LoggerTrait;
    use TranslatorTrait;

    public function __construct(
        LoggerInterface $logger,
        TranslatorInterface $translator,
        private string $isDebugMode
    ) {
        $this->setLogger($logger);
        $this->setTranslator($translator);
    }

    /**
     * @param ExceptionEvent $event
     */
    public function sendErrors(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $data = [];

        if (is_subclass_of($exception, HttpExceptionInterface::class)) {
            $statusCode = $exception->getStatusCode();
        } elseif (is_subclass_of($exception, AbstractException::class)) {
            $statusCode = $exception->getStatus();
            $data += ['type' => $exception->getType()] +
                ($exception->getAppCode() ? ['code' => $exception->getAppCode()] : []);
        } else {
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        $data += ['message' => $this->translator->trans($exception->getMessage())];

        if ($exception instanceof ValidationException) {
            $data['errors'] = $exception->getValidationErrors();
        }

        if ($this->isDebugMode && is_subclass_of($exception, ClientException::class)) {
            $data['detail'] = [
                'url' => $exception->getResponse()->getInfo('url'),
                'status' => $exception->getResponse()->getStatusCode(),
                'response' => $exception->getResponse()->toArray(false)
            ];
        }

        $response = new JsonResponse();
        $response->setStatusCode($statusCode);
        $response->setData($data);
        $event->setResponse($response);
        $event->allowCustomResponseCode();
    }

    /**
     * @param ExceptionEvent $event
     */
    public function logErrors(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $context = ['stackTrace' => $exception->getTraceAsString()];

        if (is_subclass_of($exception, ClientException::class)) {
            /** @var array<string, mixed> $errors */
            $errors = $exception->getResponse()->toArray(false);

            $context += $errors;
        }

        $this->logError($exception->getMessage(), $context);
    }
}
