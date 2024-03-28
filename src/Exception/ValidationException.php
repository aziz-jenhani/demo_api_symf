<?php

namespace App\Exception;

use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends AbstractException
{
    public function __construct(private ConstraintViolationListInterface $violations)
    {
        parent::__construct('Validation error');
    }

    public function getStatus(): int
    {
        return Response::HTTP_UNPROCESSABLE_ENTITY;
    }

    public function getType(): string
    {
        return 'validation_error';
    }

    public function getSeverity(): string
    {
        return LogLevel::INFO;
    }

    /**
     * @return array<int, array{field: string, message: string}>
     */
    public function getValidationErrors(): array
    {
        $validationErrors = [];

        /** @var ConstraintViolationInterface $violation */
        foreach ($this->violations as $violation) {
            $validationErrors[] = [
                'field' => $violation->getPropertyPath(),
                'message' => $violation->getMessage()
            ];
        }

        return $validationErrors;
    }
}
